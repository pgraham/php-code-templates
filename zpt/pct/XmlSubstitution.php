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
 * This class represents a substution tag that will encode any XML entities in
 * the output value.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class XmlSubstitution extends Substitution
{

	private $key;
	private $name;

	public function __construct($key, $name, $lineNum) {
		parent::__construct($lineNum);

		$this->key = $key;
		$this->name = $name;
	}

	public function getKey() {
		return $this->key;
	}

	public function getValue(TemplateValues $values) {
		$val = $values->getValue($this->name);
		if ($val === null) {
			throw new UndefinedValueException($this->name, $this->lineNum);
		}
		return htmlspecialchars($val, ENT_QUOTES | ENT_XML1, 'UTF-8', false);
	}

}
