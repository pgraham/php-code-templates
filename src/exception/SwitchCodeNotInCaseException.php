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
 * Exception class for code encountered in a switch statement before the first
 * case statement.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SwitchCodeNotInCaseException extends LogicException
{

	public function __construct() {
		$msg = "Code blocks cannot appear inside a switch before the first case "
		     . "statement";
		parent::__construct($msg);
	}

}
