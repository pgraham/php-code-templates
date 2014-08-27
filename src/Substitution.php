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

use zpt\pct\expression\VariableName;

/**
 * This class encapsulates base behaviour for inline tag substitutions.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class Substitution {

	private $name;
	private $filters;

	public function __construct(VariableName $name, $filters = []) {
		$this->name = $name;
		$this->filters = $filters;
	}

	public function getValue(TemplateValues $values) {
		$value = $values->getValue($this->name);
		if ($value === null) {
			throw new UndefinedValueException($this->name->__toString());
		}

		foreach ($this->filters as list($fn, $params)) {
			$value = $fn($value, $params);
		}

		return $value;
	}
}
