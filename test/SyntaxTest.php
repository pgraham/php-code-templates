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
use \zpt\pct\UndefinedValueException;
use \PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests that syntax errors are properly reported.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SyntaxText extends TestCase {

  /*
   * ===========================================================================
   * Erroneous template fragments.
   * ===========================================================================
   */

  private $caseWithoutSwitch = <<<'TMPL'
<?php
$msg = "This template contains a case statement without a switch statement";
#| case 0
TMPL;

  private $undefinedSubstitutionValue = <<<'TMPL'
<?php
$msg = "This template contains an undefined substitution values";
/*# undefined */
TMPL;

  /*
   * ===========================================================================
   * Tests
   * ===========================================================================
   */

  protected $parser;

  protected function setUp() {
    $this->parser = new CodeTemplateParser();
  }

  /**
   * @expectedException zpt\pct\ParseException
   */
  public function testCaseWithoutSwitch() {
    $this->parser->parse($this->caseWithoutSwitch);
  }

  public function testUndefinedSubstitutionValue() {
    $template = $this->parser->parse($this->undefinedSubstitutionValue);

    try {
      $resolved = $template->forValues(array());
      $this->fail("UndefinedValueException expected");
    } catch (UndefinedValueException $e) {
      $this->assertEquals('undefined', $e->getVariableName());
    }
  }

  public function testUnclosedBlock() {
    // TODO
  }

}
