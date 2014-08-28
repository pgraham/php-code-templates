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

use LogicException;

/**
 * Exception class for a switch statement that contains the default case first.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SwitchDefaultFirstException extends LogicException
{

	public function __construct() {
		parent::__construct("Default case cannot be the first switch case.");

	}
}
