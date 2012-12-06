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
 * This class represents a php tag substitution in a line of a code template.
 * A php substitution inserts the identified as value as valid php code using
 * var_export
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class PhpSubstitution extends Substitution {

  private $_name;
  private $_lineIndent;

  public function __construct($name, $lineNum, $lineIndent) {
    parent::__construct($lineNum);

    $this->_name = $name;
    $this->_lineIndent = $lineIndent;
  }

  public function getKey() {
    return '${php:' . $this->_name . '}';
  }

  public function getValue(TemplateValues $values) {
    $php = $this->_varExport($values->getValue($this->_name));
    if (preg_match('/^array\((.*)\)$/s', $php, $matches)) {
      $indent = $this->_getIndent();

      $arCtnt = $matches[1];
      $arCtnt = str_replace("\n", "\n$indent", $arCtnt);

      $php = "array ($arCtnt)";
    }
    return $php;
  }

  private function _getIndent() {
    $indent = '';
    for ($i = 0; $i < $this->_lineIndent; $i++) {
      $indent .= '  ';
    }
    return $indent;
  }

  /*
   * Private function to create a PHP representation of a given variable avoid
   * a known issue with var_export and instances of StdClass.
   */
  private function _varExport($val) {
    if (is_array($val)) {
      $vals = array();
      foreach ($val as $k => $v) {
        $vals[] = $this->_varExport($k, true) . ' => ' . $this->_varExport($v);
      }
      return 'array(' . implode(',', $vals) . ')';

    } else if (is_object($val) && get_class($val) === 'stdClass') {
      return '(object) ' . $this->_varExport((array) $val);

    } else {
      return var_export($val, true);
    }
  }

}
