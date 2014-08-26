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
class Substitution {

	private $valueName;
	private $indexes;
	private $filters;

	public function __construct(
		$valueName,
		$indexes = [],
		$filters = []
	) {
		$this->valueName = $valueName;
		$this->indexes = $indexes;
		$this->filters = $filters;
	}

	public function getValue(TemplateValues $values) {
		$value = $values->getValue($this->valueName, $this->indexes);
		if ($value === null) {
			throw new UndefinedValueException($this->valueName);
		}

		foreach ($this->filters as list($fn, $params)) {
			$value = $fn($value, $params);
		}

		return $value;
	}
}
