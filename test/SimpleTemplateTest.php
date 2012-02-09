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

use \pct\CodeTemplateParser;
use \PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests that templates that only contain substitution tags are
 * parsed and substituted as expected.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SimpleTemplateTest extends TestCase {

  /**
   * @outputBuffering disabled
   */
  public function testParser() {
    $parser = new CodeTemplateParser();

    $templateCtnt = file_get_contents(__DIR__ . '/templates/simple.template');
    $template = $parser->parse($templateCtnt);

    $keys = array('sub1', 'sub2', 'sub3');
    $vals = array('val1', 'val2', 'val3');
    $expected = str_replace(
      array_map(function ($key) { return '${' . $key . '}'; }, $keys),
      $vals,
      $templateCtnt
    );

    $actual = $template->forValues(array_combine($keys, $vals));

    $this->assertEquals($expected, $actual);
  }

}
