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
namespace zpt\pct\exception;

use LogicException;

/**
 * Exception type for template parsing exceptions.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ParseException extends LogicException
{

    private $templatePath;
    private $lineNum;

    public function __construct($templatePath, $lineNum, LogicException $cause)
    {
        $this->templatePath = $templatePath;
        $this->lineNum = $lineNum;

        $msg = "Error occured parsing $templatePath at line $lineNum";

        parent::__construct($msg, 0, $cause);
    }
}
