<?php
/*
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of php-code-templates. For the full copyright and license
 * information please view the LICENSE file that was distributed with this
 * source code.
 */
namespace zpt\pct\exception;

use InvalidArgumentException;

/**
 * Exception class for invalid conditional expression statements.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class InvalidConditionalExpressionException extends InvalidArgumentException
{

	private $expression;

	public function __construct($expression) {
		parent::__construct("Invalid conditional expression: $expression");
		$this->expression = $expression;
	}

	public function getExpression() {
		return $this->expression;
	}

}
