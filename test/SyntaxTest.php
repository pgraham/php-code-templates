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
namespace zpt\pct\test;

use \zpt\pct\CodeTemplateParser;
use \zpt\pct\SyntaxExpression;
use \PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests that syntax errors are properly reported.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SyntaxText extends TestCase {

  public function testUndefinedSubstitutionValue() {
    // TODO
  }

  public function testOutOfOrderBlockClosing() {
    // TODO
  }

}
