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

use \reed\Exception;

/**
 * This class represents an ${each:name as alias} ... ${done} substitution block
 * in a code template.
 *
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class EachBlock extends CodeBlock {

  /** The number of each blocks that have been parsed */
  public static $numBlocks = 0;

  /* The name of the value as used in the code block */
  private $_alias;

  /* The name of the value to substitute into the block */
  private $_name;

  /* The each block's substitution tag. */
  private $_tag;

  /**
   * Create a new each block representation.
   *
   * @param string $indent The amount of indentation for each substituted line.
   * @param string $expression The each expression.  Must in the form
   *   valueName as alias
   */
  public function __construct($indent, $expression) {
    parent::__construct($indent);

    $parts = preg_split('/ as /i', $expression, 2);
    if (count($parts) !== 2) {
      throw new Exception('Each block expression must be in the form'
        .' ${each:<val-name> as <alias>}');
    }

    $this->_name = trim($parts[0]);
    $this->_alias = trim($parts[1]);

    $this->_tag = '${each' . ++self::$numBlocks . '}';
  }

  /**
   * Get the block of code that should be substituted for the given set of
   * substitution values.
   *
   * @param Array $values
   * @return string The resolved code block for the given substitution values.
   */
  public function forValues(array $values, $code = null) {
    if (!array_key_exists($this->_name, $values)) {
      throw new Exception("No substitution value for {$this->_name}");
    }

    if ($values[$this->_name] === null) {
      return '';
    }

    $subVals = $values[$this->_name];
    if (!is_array($subVals)) {
      $subVals = array( $subVals );
    }

    $eaches = array();
    foreach ($subVals as $val) {
      $values[$this->_alias] = $val;

      $resolved = parent::forValues($values);
      $eaches[] = $this->_indent . $resolved;
    }

    $each = implode("\n", $eaches);

    return ltrim($each);
  }

  /**
   * Getter for the substitution tag for this block.  The parser will replace
   * the block declaration with this tag.
   *
   * @return string
   */
  public function getTag() {
    return $this->_tag;
  }

}
