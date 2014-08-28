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

use PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests the CodeLine class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class CodeLineTest extends TestCase {

  public function testSingleTagSubstitution() {
    $data = array(
      array('/*# sub */', 'val'),
      array('/*# sub */ at beginning', 'val at beginning'),
      array('at end /*# sub */', 'at end val'),
      array('the value /*# sub */ in the middle', 'the value val in the middle')
    );

    foreach ($data AS $test) {
      $codeLine = new CodeLine($test[0], 1);
      $expected = $test[1];
      $actual = $codeLine->forValues(new TemplateValues(array('sub' => 'val')));

      $this->assertEquals($expected, $actual);
    }
  }

  public function testMultipleTagSubstitution() {
    $data = array(
      array('/*# sub1 *//*# sub2 */', 'val1val2'),
      array('/*# sub1 *//*# sub2 */ at beginning', 'val1val2 at beginning'),
      array('at end /*# sub1 *//*# sub2 */', 'at end val1val2'),
      array('value /*# sub1 *//*# sub2 */ in middle', 'value val1val2 in middle'),
      array('/*# sub1 */ /*# sub2 */', 'val1 val2'),
      array('/*# sub1 */ /*# sub2 */ at beginning', 'val1 val2 at beginning'),
      array('at end /*# sub1 */ /*# sub2 */', 'at end val1 val2'),
      array('value /*# sub1 */ /*# sub2 */ in middle', 'value val1 val2 in middle'),
      array('/*# sub1 */ split /*# sub2 */', 'val1 split val2')
    );

    foreach ($data AS $test) {
      $codeLine = new CodeLine($test[0], 1);
      $expected = $test[1];
      $actual = $codeLine->forValues(new TemplateValues(array(
        'sub1' => 'val1',
        'sub2' => 'val2'
      )));

      $this->assertEquals($expected, $actual);
    }
  }

  public function testJsonSubstitution() {
    $codeLine = new CodeLine('/*# json:json */', 1);

    $expected = '{"key1":"val1","key2":[1,2,3]}';
    $actual = $codeLine->forValues(new TemplateValues(array(
      'json' => array(
        'key1' => 'val1',
        'key2' => array( 1, 2, 3 )
      )
    )));

    $this->assertEquals($expected, $actual);
  }

  public function testXmlSubstitution() {
    $codeLine = new CodeLine('/*# xml:data #*/', 1);

    $expected = 'I like to eat &lt;apples&gt; &amp; &quot;bananas&quot;';
    $actual = $codeLine->forValues(new TemplateValues(array(
      'data' => 'I like to eat <apples> & "bananas"'
    )));

    $this->assertEquals($expected, $actual);
  }

  public function testJoinSubstitution() {
    $codeLine = new CodeLine('/*# join(,):join */', 1);

    $expected = 'val1,val2,val3';
    $actual = $codeLine->forValues(new TemplateValues(array(
      'join' => array( 'val1', 'val2', 'val3' )
    )));

    $this->assertEquals($expected, $actual);
  }

  public function testJoinNotArray() {
    $codeLine = new CodeLine('/*# join(,):join */', 1);

    try {
      $codeLine->forValues(new TemplateValues(array(
        'join' => 'not an array'
      )));
      $this->fail("Expected an exception");
    } catch (UnexpectedSubstitutionValueTypeException $e) {
    }
  }

  public function testMirrorTagSyntax() {
    $codeLine = new CodeLine('/*# valname #*/', 1);

    $expected = 'a_value';
    $actual = $codeLine->forValues(new TemplateValues(array(
      'valname' => 'a_value'
    )));

    $this->assertEquals($expected, $actual);
  }

  public function testSimpleArrayTag() {
    $codeLine = new CodeLine('/*# ar[val] #*/', 1);

    $expected = 'a_value';
    $actual = $codeLine->forValues(new TemplateValues([
      'ar' => [ 'val' => 'a_value' ]
    ]));

    $this->assertEquals($expected, $actual);
  }

  public function testNestedArrayTag() {
    $codeLine = new CodeLine('/*# ar[nested][val] #*/', 1);

    $expected = 'a_value';
    $actual = $codeLine->forValues(new TemplateValues([
      'ar' => [ 'nested' => [ 'val' => 'a_value' ] ]
    ]));

    $this->assertEquals($expected, $actual);
  }

}
