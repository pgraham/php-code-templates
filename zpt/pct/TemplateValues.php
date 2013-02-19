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

use \ArrayAccess;

/**
 * This class encapsulates a set of values for template substitution.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class TemplateValues implements ArrayAccess {

  const AR_RE = '/([[:alnum:]_-]+)\[([[:alnum:]_-]+)\]/';

  private $_values;

  /**
   * Create a new TemplateValues object encapsulating the given values.
   *
   * @param array $values
   */
  public function __construct(array $values) {
    $this->_values = $values;
  }
  
  public function getValue($name) {
    if (preg_match(self::AR_RE, $name, $matches)) {
      $name = $matches[1];
      $idx = $matches[2];

      if (isset($this->_values[$name][$idx])) {
        return $this->_values[$name][$idx];
      }
    } else {
      if (isset($this->_values[$name])) {
        return $this->_values[$name];
      }
    }
    return null;
  }

  public function offsetExists($offset) {
    return isset($this->_values[$offset]);
  }

  public function offsetGet($offset) {
    return isset($this->_values[$offset])
      ? $this->_values[$offset]
      : null;
  }

  public function offsetSet($offset, $value) {
    if ($offset === null) {
      $this->_values[] = $value;
    } else {
      $this->_values[$offset] = $value;
    }
  }

  public function offsetUnset($offset) {
    unset($this->_values[$offset]);
  }
}
