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

use BadFunctionCallException;

/**
 * Exception class for substitution tag filter expressions with missing
 * parameters.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class MissingFilterExpressionArgumentException extends BadFunctionCallException
{

	private $parameterNames;

	public function __construct(array $parameterNames) {
		$this->parameterNames = $parameterNames;
	}

	public function getParameterNames() {
		return $this->parameterNames;
	}
}
