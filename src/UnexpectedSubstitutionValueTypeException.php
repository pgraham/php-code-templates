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

use UnexpectedValueException;

/**
 * Exception class for substitution filter functions that receive a value of an
 * unexpected type.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class UnexpectedSubstitutionValueTypeException extends UnexpectedValueException
{

	private $expectedType;
	private $actual;

	public function __construct($expectedType, $actual) {
		parent::__construct("Unexpected value: $actual");
		$this->expectedType = $expectedType;
		$this->actual = $actual;
	}

	public function getExpectedType() {
		return $this->expectedType;
	}

	public function getActual() {
		return $this->actual;
	}

}
