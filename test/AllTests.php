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
 * @package reed/test/generator
 */
namespace reed\test\generator;

use \PHPUnit_Framework_TestSuite as TestSuite;

require_once __DIR__ . '/../test-common.php';

/**
 * This class build the test suite for reed\generator classes.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package reed/test
 */
class AllTests {

  public static function suite() {
    $suite = new TestSuite('reed\generator test suite');

    $suite->addTestSuite('reed\test\generator\TemplateSubstitutionTest');
    $suite->addTestSuite('reed\test\generator\IfSubstitutionTest');
    $suite->addTestSuite('reed\test\generator\IfExpressionTest');
    $suite->addTestSuite('reed\test\generator\EachParserTest');

    return $suite;
  }
}
