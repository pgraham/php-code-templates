<?php
/*
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of php-code-templates. For the full copyright and license
 * information please view the LICENSE file that was distributed with this
 * source code.
 */
namespace zpt\pct;

use LogicException;

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

	public function __construct($var, $lineNum) {
		$this->var = $var;
		$this->lineNum = $lineNum;
	}

	public function addBlock(Block $block) {
		if ($this->default !== null) {
			$this->default->addBlock($block);
		} elseif (!empty($this->cases)) {
			end($this->cases)->addBlock($block);
		} else {
		  $msg = "Code blocks cannot appear inside a switch before the first "
			   . "case statement";
		  throw new LogicException($msg);
		}
	}

	public function addCase($expression, $lineNum) {
		if ($this->default !== null) {
			$msg = "Default case must be the last switch case.";
			throw new LogicException($msg);
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

	public function setDefault($lineNum) {
		if (empty($this->cases)) {
			$msg = "Default case cannot be the first switch case.";
			throw new LogicException($msg);
		}
		$this->default = new ConditionalBlock(null, $lineNum);
		end($this->cases)->setElse($this->default);
	}

	public function forValues($values) {
		if (empty($this->cases)) {
		  return null;
		}
		return reset($this->cases)->forValues($values);
	}
}
