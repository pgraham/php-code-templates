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
 * This class represents a json tag substitution in a line of a code template.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class JsonSubstitution extends Substitution {

  private $_name;

  public function __construct($name, $lineNum) {
    parent::__construct($lineNum);

    $this->_name = $name;
  }

  public function getKey() {
    return '${json:' . $this->_name . '}';
  }

  public function getValue(TemplateValues $values) {
    return json_encode($values->getValue($this->_name));
  }

}