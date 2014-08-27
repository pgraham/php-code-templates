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

use \Exception;

/**
 * Exception type for template parsing exceptions.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ParseException extends Exception
{

    private $templatePath;
    private $lineCode;
    private $lineNum;

    public function __construct($msg, $templatePath, $lineNum, $lineCode)
    {
        $this->templatePath = $templatePath;
        $this->lineCode = $lineCode;
        $this->lineNum = $lineNum;

        $msg .= "\n  at $templatePath:$lineNum"
             .  "\n  -> $lineCode";

        parent::__construct($msg);
    }
}
