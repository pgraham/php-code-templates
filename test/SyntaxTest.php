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

	/**
	 * @expectedException zpt\pct\exception\ParseException
	 */
	public function testCaseWithoutSwitch() {
		$this->parser->parse($this->caseWithoutSwitch);
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

	/**
	 * @expectedException zpt\pct\exception\ParseException
	 */
	public function testUnclosedBlock() {
		$this->parser->parse($this->unclosedBlock);
	}

}
