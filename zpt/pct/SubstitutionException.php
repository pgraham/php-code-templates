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

use \Exception;

/**
 * This class encapsulates and exception that occurs durring template value
 * substitution.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SubstitutionException extends Exception {

  private $_lineNum;

  public function __construct($message, $lineNum) {
    parent::__construct($message);
    $this->_lineNum = $lineNum;
  }

  public function getLineNum() {
    return $this->_lineNum;
  }

}
