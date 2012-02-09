<?php
/**
 * =============================================================================
 * Copyright (c) 2010, Philip Graham
 * All rights reserved.
 *
 * This file is part of Reed and is licensed by the Copyright holder under the
 * 3-clause BSD License.  The full text of the license can be found in the
 * LICENSE.txt file included in the root directory of this distribution or at
 * the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace reed\test\generator;

use \PHPUnit_Framework_TestCase as TestCase;

use \reed\generator\IfExpression;

require_once __DIR__ . '/../test-common.php';

/**
 * This class tests proper evaluation of if expressions.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class IfExpressionTest extends TestCase {

  public function testBooleanIf() {
    $if = new IfExpression('value');

    $this->assertTrue($if->isSatisfiedBy(array('value' => true)));
    $this->assertFalse($if->isSatisfiedBy(array('value' => false)));
    $this->assertFalse($if->isSatisfiedBy(array()));
  }

  public function testBooleanIndexedOperand() {
    $if = new IfExpression('value[idx]');

    $this->assertTrue($if->isSatisfiedBy(array(
      'value' => array('idx' => true)
    )));
    $this->assertFalse($if->isSatisfiedBy(array(
      'value' => array('idx' => false)
    )));
    $this->assertFalse($if->isSatisfiedBy(array(
      'value' => array()
    )));
  }

  public function testOperator() {
    $if = new IfExpression('value = value');

    $this->assertTrue($if->isSatisfiedBy(array('value' => 'value')));
    $this->assertFalse($if->isSatisfiedBy(array('value' => 'bleh')));
    $this->assertFalse($if->isSatisfiedBy(array('value' => true)));
    $this->assertFalse($if->isSatisfiedBy(array('value' => false)));
    $this->assertFalse($if->isSatisfiedBy(array()));
  }

  public function testCompositeExpression() {
    $if = new IfExpression('value = value or value = bleh');

    $this->assertTrue($if->isSatisfiedBy(array('value' => 'value')));
    $this->assertTrue($if->isSatisfiedBy(array('value' => 'bleh')));
  }

  public function testIndexedCompositeExpression() {
    $if = new IfExpression('value[idx] = value or value[idx] = bleh');

    $this->assertTrue($if->isSatisfiedBy(array(
      'value' => array('idx' => 'value')
    )));
    $this->assertTrue($if->isSatisfiedBy(array(
      'value' => array('idx' => 'bleh')
    )));
    $this->assertFalse($if->isSatisfiedBy(array(
      'value' => array('idx' => 'blah')
    )));
    $this->assertFalse($if->isSatisfiedBy(array(
      'value' => array()
    )));
    $this->assertFalse($if->isSatisfiedBy(array('value' => 'bleh')));
    $this->assertFalse($if->isSatisfiedBy(array('value' => true)));
    $this->assertFalse($if->isSatisfiedBy(array('value' => false)));
    $this->assertFalse($if->isSatisfiedBy(array()));
  }

  public function testIsSetOperator() {
    $if = new IfExpression('value ISSET');

    $this->assertTrue($if->isSatisfiedBy(array(
      'value' => 'anything'
    )));
    $this->assertFalse($if->isSatisfiedBy(array()));
  }

  public function testIsNotSetOperator() {
    $if = new IfExpression('value ISNOTSET');

    $this->assertFalse($if->isSatisfiedBy(array(
      'value' => 'anything')));
    $this->assertTrue($if->isSatisfiedBy(array()));
  }
}
