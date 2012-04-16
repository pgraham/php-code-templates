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
 * Exception for cases where a substitution value is undefined.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class UndefinedValueException extends SubstitutionException {

  public function __construct($name, $lineNum) {
    parent::__construct("Substitution value for $name is not defined",
      $lineNum);
  }
}
