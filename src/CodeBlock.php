<?php
/**
 * =============================================================================
 * Copyright (c) 2011, Philip Graham
 * All rights reserved.
 *
 * This file is part of Clarinet and is licensed by the Copyright holder under
 * the 3-clause BSD License.  The full text of the license can be found in the
 * LICENSE.txt file included in the root directory of this distribution or at
 * the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace reed\generator;

/**
 * Base class for all Clode blocks clauses.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
abstract class CodeBlock {

  /* The code that is output if the expression for this clause is satisfied */
  protected $_code;

  /* The each substitution tags */
  protected $_eaches = Array();

  /* The template's if blocks */
  protected $_ifs = Array();

  /* The base indentation level for the code block. */
  protected $_indent;

  /* The substitution tags defined in the CodeBlock */
  protected $_tags = array();

  /* The join substitution tags */
  protected $_joins = array();

  /* The JSON substitution tags */
  protected $_jsons = array();

  /**
   * Constructor. Set the indentation level of the block.
   *
   * @param string $indent String consisting of the indentation for the code
   *   block when output.  Any tab characters will be replace with 2 space
   *   characters.
   */
  public function __construct($indent) {
    $this->_indent = str_replace("\t", '  ', $indent);
  }

  /**
   * Add an each to the template.
   *
   * @param EachBlock $each
   */
  public function addEach($each) {
    $this->_eaches[] = $each;
  }

  /**
   * Add an if block to the template.
   *
   * @param IfBlock $ifBlock Encapsulated if block.
   */
  public function addIf(IfBlock $ifBlock) {
    $this->_ifs[] = $ifBlock;
  }

  /**
   * Add a join to the template.
   *
   * @param string $name The name of the value to join together before inserting
   *   into the template.
   * @param string $glue The string with which to glue together the joined
   *   values.
   */
  public function addJoin($name, $glue) {
    $glue = str_replace('\\n', "\n", $glue);

    $this->_joins[] = Array
    (
      'name' => $name,
      'glue' => $glue
    );
  }

  /**
   * Add a json substitution to the template.
   *
   * @param string $name The name of the value to encode as JSON before
   *   inserting into the template.
   */
  public function addJson($name) {
    $this->_jsons[] = $name;
  }

  /**
   * Add a simple tag to the template.
   *
   * @param string $name The name of the value to substitute into the template.
   */
  public function addTag($name) {
    $matches = array();
    if (preg_match('/([[:alnum:]_-]+)\[([[:alnum:]_-]+)\]/', $name, $matches)) {
      if (!array_key_exists($matches[1], $this->_tags)) {
        $this->_tags[$matches[1]] = array();
      }

      if (!in_array($matches[2], $this->_tags[$matches[1]])) {
        $this->_tags[$matches[1]][] = $matches[2];
      }
      
    } else if (!array_key_exists($name, $this->_tags)) {
      $this->_tags[$name] = null;
    }
  }

  /**
   * Resolve the code block by performing tag resolution.
   *
   * @param array $values The values to substitute into the code block.
   * @return string The resolved code block.
   */
  public function forValues(array $values, $code = null) {
    if ($code === null) {
      $code = $this->_code;
    }

    // Do the if and each replacements first since the if code may contain other
    // substitutions
    if (count($this->_ifs) > 0) {
      // The if statements should have been added in an order that will
      // make it possible to loop through the array once with nested ifs
      // already substituted in by the time they are reached
      foreach ($this->_ifs AS $ifBlock) {
        $toReplace    = "\${if{$ifBlock->getId()}}";
        $replacement = $ifBlock->forValues($values);

        $code = str_replace($toReplace, $replacement, $code);
      }
    }

    if (count($this->_eaches) > 0) {
      foreach ($this->_eaches AS $eachBlock) {
        $toReplace = $eachBlock->getTag();
        $replacement = $eachBlock->forValues($values);
        
        $code = str_replace($toReplace, $replacement, $code);
      }
    }

    $toReplace = array();
    $replacements = array();
    foreach ($values AS $name => $value) {
      $name = $this->_normalizeTagName($name);
      if (substr($name, 0, 2) == '${' && substr($name, -1) == '}') {
        $name = substr($name, 2, -1);
      }

      foreach ($this->_joins AS $join) {
        if ($join['name'] !== $name) {
          continue;
        }

        $tagGlue = str_replace("\n", '\\n', $join['glue']);

        $toReplace[] = "\${join:$name:$tagGlue}";
        $replacements[] = implode($join['glue'],
          (is_array($value))
            ? $value
            : Array($value)
        );
      }

      foreach ($this->_jsons AS $json) {
        if ($json !== $name) {
          continue;
        }

        $toReplace[] = "\${json:$name}";
        $replacements[] = json_encode(
          (is_array($value))
            ? $value
            : Array($value)
        );
      }

      foreach ($this->_tags AS $var => $indexes) {
        if ($var !== $name) {
          continue;
        }

        if ($indexes === null) {
          $toReplace[] = "\${{$var}}";
          $replacements[] = $value;
        } else {
          foreach ($indexes AS $idx) {
            $toReplace[] = "\${{$var}[$idx]}";
            $replacements[] = $value[$idx];
          }
        }
      }
    }

    return str_replace($toReplace, $replacements, $code);
  }

  /**
   * Getter for the code block's unresolved code.
   *
   * @return string
   */
  public function getCode() {
    return $this->_code;
  }

  /**
   * Set the raw unresolved code for this block.
   *
   * @param string $code The code for the block, including indentation.  Any tab
   *   characters will be replace with two space characters.
   */
  public function setCode($code) {
    $code = CodeBlockParser::parse($code, $this);

    // Replace tab characters with spaces
    $code = str_replace("\t", '  ', $code);

    // Assume that code is defined at or deeper than the code block declaration.
    // Any indentation deeper than the base needs to be preserved, so the base
    // indentation is determined to be that of the first line in the block.
    // This base indentation level is then used to re-indent the code to a base
    // level that is the same as the block declaration.
    $matches = array();
    if (preg_match('/^([ ]*)/', $code, $matches)) {
      $baseIndent = $matches[1];
      $dode = preg_replace(
        "/^$baseIndent/m",
        $this->_indent,
        $code);
    }

    // When replacement happens, indent for the block declaration isn't replaced
    // so we don't want any indentation at the start of the first line of the
    // code
    $this->_code = ltrim($code);
  }

  /**
   * Normalize the name of a substitution tag.  This will strip off any
   * surrounding ${} characters.
   */
  protected function _normalizeTagName($name) {
    if (substr($name, 0, 2) == '${' && substr($name, -1) == '}') {
      return substr($name, 2, -1);
    }
    return $name;
  }

}
