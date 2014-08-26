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

use ArrayAccess;

/**
 * This class encapsulates a set of values for template substitution.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class TemplateValues implements ArrayAccess
{

	private $values;

	/**
	 * Create a new TemplateValues object encapsulating the given values.
	 *
	 * @param array $values
	 */
	public function __construct(array $values) {
		$this->values = $values;
	}

	public function getValue($name, array $indexes = []) {
		if (!isset($this->values[$name])) {
			return null;
		}

		$value = $this->values[$name];
		foreach ($indexes as $idx) {
			if (is_array($value) && isset($value[$idx])) {
				$value = $value[$idx];
			} else {
				return null;
			}
		}
		return $value;
	}

	public function offsetExists($offset) {
		return isset($this->values[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->values[$offset])
			? $this->values[$offset]
			: null;
	}

	public function offsetSet($offset, $value) {
		if ($offset === null) {
			$this->values[] = $value;
		} else {
			$this->values[$offset] = $value;
		}
	}

	public function offsetUnset($offset) {
		unset($this->values[$offset]);
	}
}
