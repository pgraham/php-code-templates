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
use \zpt\pct\TemplateValues;
use \PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests proper template parsing and substitution for if templates.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class IfSubstitutionTest extends TestCase {

  public function testBooleanIf() {
    $parser = new CodeTemplateParser();

    $ifCtnt = "#{if boolval\nI am true\n#{ else\nI am false\n#}";
    $template = $parser->parse($ifCtnt);

    // Assert structure of parsed code template
    $blocks = $template->getBlocks();
    $this->assertCount(1, $blocks);

    // Assert structure of parsed if block
    $ifBlock = $blocks[0];
    $this->assertInstanceOf('zpt\pct\ConditionalBlock', $ifBlock);
    $this->assertCount(1, $ifBlock->getBlocks());

    $ifExpression = $ifBlock->getExpression();
    $this->assertNotNull($ifExpression);
    $this->assertInstanceOf('zpt\pct\ConditionalExpression', $ifExpression);

    // Assert value substitution for if block
    $vals = new TemplateValues(array( 'boolval' => true ));
    $this->assertTrue($ifExpression->isSatisfiedBy($vals));

    $expected = "I am true";
    $actual = $template->forValues($vals);
    $this->assertEquals($expected, $actual);

    // Assert structure of else block
    $elseBlock = $ifBlock->getElse();
    $this->assertNotNull($elseBlock);
    $this->assertInstanceOf('zpt\pct\ConditionalBlock', $elseBlock);
    $this->assertCount(1, $elseBlock->getBlocks());
    $this->assertNull($elseBlock->getExpression());

    // Assert value sustitution for else block
    $vals = new TemplateValues(array( 'boolval' => false ));

    $expected = "I am false";
    $actual = $template->forValues($vals);
    $this->assertEquals($expected, $actual);
  }

  public function testOpIfElseIf() {
    $parser = new CodeTemplateParser();

    $ifCtnt = 
      "#{ if val = val1\n" .
      "I am val1\n" .
      "#{ elseif val = val2\n" .
      "I am val2\n" .
      "#{ elseif val = val3\n" .
      "I am val3\n" .
      "#{ else\n" .
      "I am another value\n" .
      "#}";
    $template = $parser->parse($ifCtnt);

    // Assert Structure of parsed code template
    $blocks = $template->getBlocks();
    $this->assertCount(1, $blocks);

    // Assert structure of parsed if block
    $if = $blocks[0];
    $this->assertInstanceOf('zpt\pct\ConditionalBlock', $if);
    $this->assertCount(1, $if->getBlocks());

    $ifExp = $if->getExpression();
    $this->assertNotNull($ifExp);

    $elseIf1 = $if->getElse();
    $this->assertNotNull($elseIf1);
    $this->assertInstanceOf('zpt\pct\ConditionalBlock', $elseIf1);
    $this->assertCount(1, $elseIf1->getBlocks());

    $elseIf1Exp = $elseIf1->getExpression();
    $this->assertNotNull($elseIf1Exp);

    $elseIf2 = $elseIf1->getElse();
    $this->assertNotNull($elseIf2);
    $this->assertInstanceOf('zpt\pct\ConditionalBlock', $elseIf2);
    $this->assertCount(1, $elseIf2->getBlocks());

    $elseIf2Exp = $elseIf2->getExpression();
    $this->assertNotNull($elseIf2Exp);

    $else = $elseIf2->getElse();
    $this->assertNotNull($else);
    $this->assertInstanceOf('zpt\pct\ConditionalBlock', $else);
    $this->assertCount(1, $else->getBlocks());
    $this->assertNull($else->getElse());
    $this->assertNull($else->getExpression());


    // Test value substitution
    $vals = new TemplateValues(array('val' => 'val1'));
    $this->assertTrue($ifExp->isSatisfiedBy($vals));
    $this->assertFalse($elseIf1Exp->isSatisfiedBy($vals));
    $this->assertFalse($elseIf2Exp->isSatisfiedBy($vals));

    $expected = 'I am val1';
    $actual = $template->forValues($vals);
    $this->assertEquals($expected, $actual);

    $vals = new TemplateValues(array('val' => 'val2'));
    $this->assertFalse($ifExp->isSatisfiedBy($vals));
    $this->assertTrue($elseIf1Exp->isSatisfiedBy($vals));
    $this->assertFalse($elseIf2Exp->isSatisfiedBy($vals));

    $expected = 'I am val2';
    $actual = $template->forValues($vals);
    $this->assertEquals($expected, $actual);

    $vals = new TemplateValues(array('val' => 'val3'));
    $this->assertFalse($ifExp->isSatisfiedBy($vals));
    $this->assertFalse($elseIf1Exp->isSatisfiedBy($vals));
    $this->assertTrue($elseIf2Exp->isSatisfiedBy($vals));

    $expected = 'I am val3';
    $actual = $template->forValues($vals);
    $this->assertEquals($expected, $actual);

    $vals = new TemplateValues(array('val' => 'val4'));
    $this->assertFalse($ifExp->isSatisfiedBy($vals));
    $this->assertFalse($elseIf1Exp->isSatisfiedBy($vals));
    $this->assertFalse($elseIf2Exp->isSatisfiedBy($vals));

    $expected = 'I am another value';
    $actual = $template->forValues($vals);
    $this->assertEquals($expected, $actual);
  }

  public function testNestedIfs() {
    $parser = new CodeTemplateParser();

    $ifCtnt = <<<EOT
#{ if outer
  #{ if inner
    INNER
  #{ else
    NOT INNER
  #}
#}
EOT;
    $template = $parser->parse($ifCtnt);

    $this->assertEquals('INNER', $template->forValues(array(
      'outer' => true,
      'inner' => true
    )));

    $this->assertEquals('NOT INNER', $template->forValues(array(
      'outer' => true,
      'inner' => false
    )));

    $this->assertEquals('', $template->forValues(array(
      'outer' => false
    )));

  }

  public function testUnsatisfiedIf() {
    $parser = new CodeTemplateParser();

    $ifCtnt = <<<IF
Before if.
#{ if value
  Output
#}
After if.
IF;

    $template = $parser->parse($ifCtnt);

    $expected = "Before if.\nAfter if.";
    $actual = $template->forValues( array() );

    $this->assertEquals($expected, $actual);
  }
}
