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
 * This class tests proper template parsing and substitution for each templates.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class EachSubstitutionTest extends TestCase {

  public function testEach() {
    $parser = new CodeTemplateParser();

    $eachCtnt = "\${each:itr as i}\n\${i}\n\${done}";
    $template = $parser->parse($eachCtnt);

    // Assert structure of parsed template
    $blocks = $template->getBlocks();
    $this->assertCount(1, $blocks);

    $eachBlock = $blocks[0];
    $this->assertInstanceOf('zpt\pct\EachBlock', $eachBlock);
    $this->assertCount(1, $eachBlock->getBlocks());

    // Assert value substitution for each block
    $vals = new TemplateValues(array(
      'itr' => array( 'val1', 'val2', 'val3', 'val4' )
    ));

    $expected = "val1\nval2\nval3\nval4";
    $actual = $template->forValues($vals);
    $this->assertEquals($expected, $actual);
  }

  public function testEachEmptySet() {
    $parser = new CodeTemplateParser();

    $eachCtnt = <<<TMPL
Iteration to follow:
\${each:itr as i}
\${i}
\${done}
Post Iteration.
TMPL;
    $template = $parser->parse($eachCtnt);

    $expected = "Iteration to follow:\nPost Iteration.";
    $actual = $template->forValues(array( 'itr' => array() ));
    $this->assertEquals($expected, $actual);
  }

}
