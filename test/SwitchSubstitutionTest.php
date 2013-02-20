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
 * @license https://github.com/pgraham/php-code-templates/LICENSE.txt
 */
namespace zpt\pct\test;

use \zpt\pct\CodeTemplateParser;
use \PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests proper template parsing and substitution for switch control
 * statements.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SwitchSubstitutionTest extends TestCase
{

    public function testSwitch() {
        $parser = new CodeTemplateParser();

        $tmpl = <<<TMPL
<?php
\$msg = "This template contains a switch template control statement.";
#{ switch var
#| case < 0
  Less than zero
#| case 0
  Equal to zero
#| case > 0
  Greater than zero
#}
TMPL;

        $template = $parser->parse($tmpl);

        $expectedBase = <<<EXPT
<?php
\$msg = "This template contains a switch template control statement.";

EXPT;

        $expected = $expectedBase . "Less than zero";
        $this->assertEquals($expected, $template->forValues(array(
            'var' => -1
        )));

        $expected = $expectedBase . "Equal to zero";
        $this->assertEquals($expected, $template->forValues(array(
            'var' => 0
        )));

        $expected = $expectedBase . "Greater than zero";
        $this->assertEquals($expected, $template->forValues(array(
            'var' => 1
        )));
    }

    /**
     * @expectedException zpt\pct\ParseException
     */
    public function testCodeBeforeFirstCase()
    {

        $parser = new CodeTemplateParser();

        $tmpl = <<<TMPL
<?php
\$msg = "This template contains a switch template with code before the first
         case";
#{ switch owned
  \$commonVal = "I belong to %s!";
#| case Joe
  echo sprintf(\$commonVal, "Peter");
#}
TMPL;

        $template = $parser->parse($tmpl);

    }

}
