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
 * This class tests that substituted code is properly indented.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class IndentTest extends TestCase {

  public function testNoIndentSingleLine() {
    $parser = new CodeTemplateParser();

    $tmpl = <<<TMPL
\${if:boolval}
  I am true.
\${fi}
TMPL;

    $template = $parser->parse($tmpl);

    $expected = 'I am true.';
    $actual = $template->forValues(array( 'boolval' => true));
    $this->assertEquals($expected, $actual);
  }

  public function testNoIndentMultipleLines() {
    $parser = new CodeTemplateParser();

    $tmpl = <<<TMPL
\${if:boolval}
  I am true.
  Seriously.
\${fi}
TMPL;

    $template = $parser->parse($tmpl);

    $expected = "I am true.\nSeriously.";
    $actual = $template->forValues(array( 'boolval' => true));
    $this->assertEquals($expected, $actual);
  }

  public function testSingleIndentSingleLine() {
    $parser = new CodeTemplateParser();

    $tmpl = <<<TMPL
foreach (\$i = 0; \$i < 10; \$i++) {
  \${each:stmts as stmt}
    \${stmt};
  \${done}
}
TMPL;

    $template = $parser->parse($tmpl);

    $expected = <<<EXPT
foreach (\$i = 0; \$i < 10; \$i++) {
  echo \$i;
}
EXPT;

    $actual = $template->forValues(array(
      'stmts' => array( 'echo $i' )
    ));
    $this->assertEquals($expected, $actual);
  }

  public function testSingleIndentMultipleLines() {
    $parser = new CodeTemplateParser();

    $tmpl = <<<TMPL
foreach (\$i = 0; \$i < 10; \$i++) {
  \${each:stmts as stmt}
    \${stmt};
    \$v = 'another stmt';
  \${done}
}
TMPL;

    $template = $parser->parse($tmpl);

    $expected = <<<EXPT
foreach (\$i = 0; \$i < 10; \$i++) {
  echo \$i;
  \$v = 'another stmt';
}
EXPT;

    $actual = $template->forValues(array(
      'stmts' => array( 'echo $i' )
    ));
    $this->assertEquals($expected, $actual);
  }
}
