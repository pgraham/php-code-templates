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
namespace pct;

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

  private $_line;
  private $_lineNum;

  private $_tags;

  public function __construct($line, $lineNum) {
    $this->_line = $line;
    $this->_lineNum = $lineNum;
  }

  public function forValues(array $values) {
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

    return str_replace($search, $replace, $this->_line);
  }

  private function _parseTags() {
    $this->_tags = array();

    // Parse joins
    if (preg_match_all(self::JOIN_RE, $this->_line, $joins, PREG_SET_ORDER)) {
      foreach ($joins AS $join) {
        $this->_tags[] = new JoinSubstitution($join[1], $join[2]);
      }
    }

    // Parse JSON outputs
    if (preg_match_all(self::JSON_RE, $this->_line, $jsons, PREG_SET_ORDER)) {
      foreach ($jsons AS $json) {
        $this->_tags[] = new JsonSubstitution($json[1]);
      }
    }

    // Parse normal substitutions
    if (preg_match_all(self::TAG_RE, $this->_line, $tags, PREG_SET_ORDER)) {
      foreach ($tags AS $tag) {
        $this->_tags[] = new TagSubstitution($tag[1]);
      }
    }
  }
}
