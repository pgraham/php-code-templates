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

use zpt\pct\parser\VariableNameParser;

/**
 * This class represents an each block in a code template.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class EachBlock extends CompositeBlock {

	/* The name of the value as used in the code block */
	private $alias;

	/* The name of the value to substitute into the block */
	private $name;

	/**
	 * Create a new each block representation.
	 *
	 * @param string $indent The amount of indentation for each substituted line.
	 * @param string $expression The each expression.  Must in the form
	 *	 valueName as alias
	 */
	public function __construct($expression, $lineNum) {
		parent::__construct($lineNum);

		$parts = preg_split('/\s+as\s+/i', $expression, 2);
		if (count($parts) !== 2) {
			// TODO This should be a parse exception
			throw new SubstitutionException($lineNum);
		}

		$this->name = VariableNameParser::parse(trim($parts[0]));
		$this->alias = trim($parts[1]);
	}

	/**
	 * Get the block of code that should be substituted for the given set of
	 * substitution values.
	 *
	 * @param Array $values
	 * @return string The resolved code block for the given substitution values.
	 */
	public function forValues($values) {
		$itr = $values->getValue($this->name->getName(), $this->name->getIndexes());
		if ($itr === null) {
			throw new UndefinedValueException($this->name->__toString());
		}

		if (!is_array($itr)) {
			throw new UnexpectedSubstitutionValueTypeException('array', $itr);
		}

		// If the given value is an empty array return null to avoid extra white
		// space in the containing code block
		if (count($itr) === 0) {
			return null;
		}

		$eaches = array();
		foreach ($itr as $val) {
			$values[$this->alias] = $val;

			$eaches[] = parent::forValues($values);
			unset($values[$this->alias]);
		}

		return implode("\n", $eaches);
	}

}
