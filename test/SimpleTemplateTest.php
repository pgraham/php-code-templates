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
 * This class tests that templates that only contain substitution tags are
 * parsed and substituted as expected.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SimpleTemplateTest extends TestCase {

  public function testSingleCodeBlockWithTagSubstitutions() {
    $parser = new CodeTemplateParser();

    $templateCtnt = file_get_contents(__DIR__ . '/templates/simple.tmpl.php');
    $template = $parser->parse($templateCtnt);

    $keys = array('sub1', 'sub2', 'sub3', 'inline');
    $vals = array('val1', 'val2', 'val3', 'inline');
    $expected = file_get_contents(__DIR__ . '/templates/simple.expected');

    $actual = $template->forValues(new TemplateValues(
      array_combine($keys, $vals)
    ));

    $this->assertEquals($expected, $actual);
  }

}
