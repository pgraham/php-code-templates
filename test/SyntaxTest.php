<?php
/*
 * Copyright (c) 2012 - 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of php-code-templates. For the full copyright and license
 * information please view the LICENSE file that was distributed with this
 * source code.
 */
namespace zpt\pct;

use PHPUnit_Framework_TestCase as TestCase;

use zpt\pct\exception\ParseException;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests that syntax errors are properly reported.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SyntaxText extends TestCase
{

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

	private $defaultWithoutSwitch = <<<'TMPL'
<?php
$msg = "This template contains a default statement without a switch statement";
#| default
TMPL;

	private $switchCodeBeforeCase = <<<'TMPL'
<?php
$msg = "This template contains a switch statement with code appearing before the
first case statement.";
#{ switch var 
 doSomething(/*# some_param #*/);
#| case < 0
 doSomethingWhenVarIsLessThenZero(); 
#}
TMPL;

	private $switchCaseAfterDefault = <<<'TMPL'
<?php
$msg = "This template contains a switch statement with a case after default.";
#{ switch var
#| case < 0
  doSomething(/*# var #*/);
#| default
  doDefault();
#| case > 0
  doSomethingElse(/*# var #*/);
#}
TMPL;
	private $switchDefaultFirst = <<<'TMPL'
<?php
$msg = "This template contains a switch statement with the default case first.";
#{ switch var
#| default
  doTheDefaultThing();
#}
TMPL;

	private $undefinedSubstitutionValue = <<<'TMPL'
<?php
$msg = "This template contains an undefined substitution values";
/*# undefined */
TMPL;

	private $unclosedBlock = <<<'TMPL'
<?php
$msg = "This tempate contains an unclosed block";
#{ if var = val
  #{ if foo = bar
    $msg .= "foo is equal to bar";
#}
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

	public function testCaseWithoutSwitch() {
		try {
			$this->parser->parse($this->caseWithoutSwitch);
			$this->fail("Expected a ParseException");
		} catch (ParseException $e) {
			$previous = $e->getPrevious();
			$this->assertInstanceOf(
				'zpt\pct\exception\UnexpectedCaseException',
				$previous
			);
		}
	}

	public function testDefaultWithoutSwitch() {
		try {
			$this->parser->parse($this->defaultWithoutSwitch);
			$this->fail("Expected a ParseException");
		} catch (ParseException $e) {
			$previous = $e->getPrevious();
			$this->assertInstanceOf(
				'zpt\pct\exception\UnexpectedDefaultException',
				$previous
			);
		}
	}

	public function testSwitchCodeBeforeCase() {
		try {
			$this->parser->parse($this->switchCodeBeforeCase);
			$this->fail("Expected a ParseException");
		} catch (ParseException $e) {
			$previous = $e->getPrevious();
			$this->assertInstanceOf(
				'zpt\pct\exception\SwitchCodeNotInCaseException',
				$previous
			);
		}
	}

	public function testSwitchCaseAfterDefault() {
		try {
			$this->parser->parse($this->switchCaseAfterDefault);
			$this->fail("Expected a ParseException");
		} catch (ParseException $e) {
			$previous = $e->getPrevious();
			$this->assertInstanceOf(
				'zpt\pct\exception\SwitchCaseAfterDefaultException',
				$previous
			);
		}
	}

	public function testSwitchDefaultFirst() {
		try {
			$this->parser->parse($this->switchDefaultFirst);
			$this->fail("Expected a ParseException");
		} catch (ParseException $e) {
			$previous = $e->getPrevious();
			$this->assertInstanceOf(
				'zpt\pct\exception\SwitchDefaultFirstException',
				$previous
			);
		}
	}

	public function testUndefinedSubstitutionValue() {
		$template = $this->parser->parse($this->undefinedSubstitutionValue);

		try {
			$resolved = $template->forValues(array());
			$this->fail("UndefinedValueException expected");
		} catch (SubstitutionException $e) {
			$previous = $e->getPrevious();
			$this->assertInstanceOf('zpt\pct\UndefinedValueException', $previous);
			$this->assertEquals('undefined', $previous->getVariableName());
		}
	}

	public function testUnclosedBlock() {
		try {
			$this->parser->parse($this->unclosedBlock);
			$this->fail("Expected a ParseException");
		} catch (ParseException $e) {
			$previous = $e->getPrevious();
			$this->assertInstanceOf(
				'zpt\pct\exception\UnclosedBlockException',
				$previous
			);
		}
	}

}
