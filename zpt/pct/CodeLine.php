<?php
/**
 * =============================================================================
 * Copyright (c) 2012, Philip Graham
 * All rights reserved.
 *
 * This file is part of php-code-templates and is licensed by the Copyright
 * holder under the 3-clause BSD License.  The full text of the license can be
 * found in the LICENSE.txt file included in the root directory of this
 * distribution or at the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace zpt\pct;

/**
 * This class encapsulates a line of code to be output when performing value
 * substitution on a template.  Actual output and number of times it is output
 * depends on the given substitution values and where in the template block
 * structure it appears.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class CodeLine {

  const JOIN_RE = '/\$\{join:([^:]+):([^\}]+)\}/';
  const JSON_RE = '/\$\{json:([^\}]+)\}/';
  const TAG_RE  = '/\$\{([[:alnum:]\[\]_-]+)\}/';

  private $_indent = 0;
  private $_line;
  private $_lineNum;

  private $_tags;

  public function __construct($line, $lineNum) {
    $this->_line = trim($line);
    $this->_lineNum = $lineNum;
  }

  public function forValues(TemplateValues $values) {
    // Only parse tags once and only if the line is actually output
    if ($this->_tags === null) {
      $this->_parseTags();
    }

    $search = array();
    $replace = array();
    foreach ($this->_tags as $tag) {
      $search[] = $tag->getKey();
      $replace[] = $tag->getValue($values);
    }

    $r = $this->_getIndent() . str_replace($search, $replace, $this->_line);
    return $r;
  }

  public function getLineNum() {
    return $this->_lineNum;
  }

  public function setIndent($indent) {
    $this->_indent = $indent;
  }

  private function _getIndent() {
    $indent = '';
    for ($i = 0; $i < $this->_indent; $i++) {
      $indent .= '  ';
    }
    return $indent;
  }

  private function _parseTags() {
    $this->_tags = array();

    // Parse joins
    if (preg_match_all(self::JOIN_RE, $this->_line, $joins, PREG_SET_ORDER)) {
      foreach ($joins AS $join) {
        $tag = new JoinSubstitution($join[1], $join[2], $this->_lineNum);
        $this->_tags[] = $tag;
      }
    }

    // Parse JSON outputs
    if (preg_match_all(self::JSON_RE, $this->_line, $jsons, PREG_SET_ORDER)) {
      foreach ($jsons AS $json) {
        $tag = new JsonSubstitution($json[1], $this->_lineNum);
        $this->_tags[] = $tag;
      }
    }

    // Parse normal substitutions
    if (preg_match_all(self::TAG_RE, $this->_line, $tags, PREG_SET_ORDER)) {
      foreach ($tags AS $tag) {
        $tag = new TagSubstitution($tag[1], $this->_lineNum);
        $this->_tags[] = $tag;
      }
    }
  }
}
