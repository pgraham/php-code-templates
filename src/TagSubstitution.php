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
 * This class represents a simple tag substitution in a line of a code template.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class TagSubstitution {

  const AR_RE = '/([[:alnum:]_-]+)\[([[:alnum:]_-]+)\]/';

  private $_name;

  public function __construct($name) {
    $this->_name = $name;
  }
  
  public function getKey() {
    return '${'. $this->_name . '}';
  }

  public function getValue(array $values) {
    if (preg_match(self::AR_RE, $this->_name, $matches)) {
      $name = $matches[1];
      $idx = $matches[2];

      if (isset($values[$name][$idx])) {
        return $values[$name][$idx];
      }
    } else {
      if (isset($values[$this->_name])) {
        return $values[$this->_name];
      }
    }
    return null;
  }
}
