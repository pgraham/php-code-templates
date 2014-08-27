<?php
/*
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of php-code-templates. For the full copyright and license
 * information please view the LICENSE file that was distributed with this
 * source code.
 */
namespace zpt\pct\expression;

/**
 * This class encapsulates a substitution value variable name.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class VariableName
{

	private $name;
	private $indexes;

	/**
	 * Create a variable name representation
	 *
	 * @param string $name
	 *   The name of the substitution value from which to retrieve the value of
	 *   the variable.
	 * @param string[] $indexes
	 *   Index path to the variable value when stored in a (possibly nested) array
	 *   value.
	 */
	public function __construct($name, $indexes = []) {
		$this->name = $name;
		$this->indexes = $indexes;
	}

	public function __toString() {
		$s = $this->name;
		if (count($this->indexes) > 0) {
			$s .= '[' . implode('][', $this->indexes) . ']';
		}
		return $s;
	}

	public function getName() {
		return $this->name;
	}

	public function getIndexes() {
		return $this->indexes;
	}
}
