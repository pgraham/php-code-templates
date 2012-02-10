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
 * This class encapsulates an exception that occurs when the value given for
 * a substitution tag is not the expected type.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class InvalidTypeException extends SubstitutionException {

  /**
   * Create a new InvalidTypeException.
   *
   * @param string $name
   * @param string $expectedType
   * @param mixed $actualValue
   * @param integer $lineNum
   */
  public function __construct($name, $expectedType, $actualValue, $lineNum) {
    parent::__construct(
      "Substitution value for $name was an unexpected type.\n" .
      "  Expected: $expectedType.\n" .
      "  Found: $actualValue (" . gettype($actualValue) . ")",
      $lineNum);
  }

}
