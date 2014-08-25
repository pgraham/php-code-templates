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

/**
 * This class encapsulates base behaviour for inline tag substitutions.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
abstract class Substitution {

	protected $lineNum;

	protected function __construct($lineNum) {
		$this->lineNum = $lineNum;
	}
}
