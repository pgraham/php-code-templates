<?php
/*
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of php-code-templates. For the full copyright and license
 * information please view the LICENSE file that was distributed with this
 * source code.
 */
namespace zpt\pct\parser;

/**
 * Parser for variable names.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class VariableNameParser
{

	const NAME_RE = '/(\w+)(?:\[([\w[\]]+)\])*/';

	public static function parse($nameDef) {
		if (preg_match(self::NAME_RE, $nameDef, $matches)) {
			$name = $matches[1];
			$indexes = [];
			if (isset($matches[2])) {
				$indexes = explode('][', $matches[2]);
			}
			return [ 'name' => $name, 'indexes' => $indexes ];
		}
		return null;
	}
}
