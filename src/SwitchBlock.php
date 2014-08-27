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

/**
 * CompositeBlock for switch statments.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SwitchBlock implements Block
{

    private $cases = array();
    private $default;
    private $var;
    private $lineNum;

    public function __construct($var, $lineNum)
    {
        $this->var = $var;
        $this->lineNum = $lineNum;
    }

    public function addBlock(Block $block)
    {
        if ($this->default !== null) {
            $this->default->addBlock($block);
        } elseif (!empty($this->cases)) {
            end($this->cases)->addBlock($block);
        } else {
          $msg = "Code blocks cannot appear inside a switch before the first "
               . "case statement";
          throw new StructureException($msg);
        }
    }

    public function addCase($expression, $lineNum)
    {
        if ($this->default !== null) {
            $msg = "Default case must be the last switch case.";
            throw new StructureException($msg);
        }

        $parts = explode(' ', $expression);
        if (!ConditionalExpression::isValidOperator($parts[0])) {
            $expression = "= $expression";
        }
        $expression = "$this->var $expression";

        $block = new ConditionalBlock($expression, $lineNum);

        if (!empty($this->cases)) {
            end($this->cases)->setElse($block);
        }
        $this->cases[] = $block;
    }

    public function setDefault($lineNum)
    {
        if (empty($this->cases)) {
            $msg = "Default case cannot be the first switch case.";
            throw new StructureException($msg);
        }
        $this->default = new ConditionalBlock(null, $lineNum);
        end($this->cases)->setElse($this->default);
    }

    public function forValues($values)
    {
        if (empty($this->cases)) {
          return null;
        }
        return reset($this->cases)->forValues($values);
    }
}
