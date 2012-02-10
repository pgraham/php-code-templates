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
namespace pct\test;

use \pct\ConditionalExpression;
use \pct\TemplateValues;
use \PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests proper evaluation of if expressions.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ConditionalExpressionTest extends TestCase {

  public function testBooleanIf() {
    $if = new ConditionalExpression('value');

    $this->assertTrue($if->isSatisfiedBy(new TemplateValues(array(
      'value' => true
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array(
      'value' => false
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array())));
  }

  public function testBooleanIndexedOperand() {
    $if = new ConditionalExpression('value[idx]');

    $this->assertTrue($if->isSatisfiedBy(new TemplateValues(array(
      'value' => array('idx' => true)
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array(
      'value' => array('idx' => false)
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array(
      'value' => array()
    ))));
  }

  public function testOperator() {
    $if = new ConditionalExpression('value = value');

    $this->assertTrue($if->isSatisfiedBy(new TemplateValues(array(
      'value' => 'value'
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array(
      'value' => 'bleh'
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array(
      'value' => true
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array(
      'value' => false
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array())));
  }

  public function testCompositeExpression() {
    $if = new ConditionalExpression('value = value or value = bleh');

    $this->assertTrue($if->isSatisfiedBy(new TemplateValues(array(
      'value' => 'value'
    ))));
    $this->assertTrue($if->isSatisfiedBy(new TemplateValues(array(
      'value' => 'bleh'
    ))));
  }

  public function testIndexedCompositeExpression() {
    $if = new ConditionalExpression('value[idx] = value or value[idx] = bleh');

    $this->assertTrue($if->isSatisfiedBy(new TemplateValues(array(
      'value' => array('idx' => 'value')
    ))));
    $this->assertTrue($if->isSatisfiedBy(new TemplateValues(array(
      'value' => array('idx' => 'bleh')
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array(
      'value' => array('idx' => 'blah')
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array(
      'value' => array()
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array(
      'value' => 'bleh'
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array(
      'value' => true
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array(
      'value' => false
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array())));
  }

  public function testIsSetOperator() {
    $if = new ConditionalExpression('value ISSET');

    $this->assertTrue($if->isSatisfiedBy(new TemplateValues(array(
      'value' => 'anything'
    ))));
    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array())));
  }

  public function testIsNotSetOperator() {
    $if = new ConditionalExpression('value ISNOTSET');

    $this->assertFalse($if->isSatisfiedBy(new TemplateValues(array(
      'value' => 'anything'
    ))));
    $this->assertTrue($if->isSatisfiedBy(new TemplateValues(array())));
  }
}
