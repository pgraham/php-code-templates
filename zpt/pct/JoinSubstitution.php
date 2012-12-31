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
 * This class represents a join tag substitution in a line of a code template.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class JoinSubstitution extends Substitution {

  private $_name;
  private $_glue;

  public function __construct($name, $glue, $lineNum) {
    parent::__construct($lineNum);

    $this->_name = $name;
    $this->_glue = $glue;
  }

  public function getKey() {
    return '${join:' . $this->_name . ':' . $this->_glue . '}';
  }

  public function getValue(TemplateValues $values) {
    $val = $values->getValue($this->_name);
    if ($val === null) {
      return '';
    }

    if (!is_array($val)) {
      throw new InvalidTypeException($this->_name, 'array', $val,
        $this->lineNum);
    }

    return implode($this->_glue, $val);
  }
}
