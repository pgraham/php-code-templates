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

use Exception;

/**
 * This class encapsulates expression evaluation for a conditional clause of a
 * {@link ConditionalBlock}.
 *
 *	 NOTE: There is a known issue that using any of the operators, or the words
 *				 'and' or 'or' as comparison values will not behave as expected.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ConditionalExpression
{

	/* Whether or not the class has been statically constructed. */
	private static $initialized = false;

	/*
	 * Non-capturing regular expression for all supported operators. Any
	 * strings matched by this regexp must have a matching entry in opEvaluators.
	 */
	private static $ops = '(?:=|>|>=|<|<=|!=|ISSET|ISNOTSET)';

	private static $opEvaluators;

	/*
	 * Static constructor.	Will happen the first time an instance of this class
	 * is created.
	 */
	private static function initialize() {
		self::$initialized = true;

		self::$opEvaluators = array(
			// '=' Evaluator
			'=' => function ($a, $b) {
				return $a === $b;
			},

			// '>' Evaluator
			'>' => function ($a, $b) {
				return $a > $b;
			},

			// '>=' Evaluator
			'>=' => function ($a, $b) {
				return $a >= $b;
			},

			// '<' Evaluator
			'<' => function ($a, $b) {
				return $a < $b;
			},

			// '<=' Evaluator
			'<=' => function ($a, $b) {
				return $a <= $b;
			},

			// '!=' Evaluator
			'!=' => function ($a, $b) {
				return $a !== $b;
			},

			'ISSET' => function ($a, $b) {
				return $a !== null;
			},

			'ISNOTSET' => function ($a, $b) {
				return $a === null;
			}
		);
	}

	/**
	 * Static function to determine if the specified string is a valid operator.
	 *
	 * @param string $op
	 * @return boolean
	 */
	public static function isValidOperator($op) {
		return preg_match('/' . self::$ops . '/', trim($op));
	}

	/*
	 * ===========================================================================
	 * Instance
	 * ===========================================================================
	 */

	private $_conditions;

	/**
	 * Create a new ConditionalExpression.
	 *
	 * @param string $expression Unparsed expression string.
	 */
	public function __construct($expression) {
		if (!self::$initialized) {
			self::initialize();
		}

		$ops = self::$ops;
		$varRe = '[[:alnum:]_-]+(?:\[[[:alnum:]_-]+\])?';
		$logicRe = "\s*($varRe\s*(?:$ops\s*$varRe)?)\s+(or|and)\s+(.*)\s*";

		$curGroup = array();

		$exp = $expression;
		while ($exp !== null) {
			$matches = array();
			if (preg_match("/$logicRe/", $exp, $matches)) {
				$comp = trim($matches[1]);
				$logic = trim($matches[2]);
				$exp = trim($matches[3]);

				$cond = $this->buildCondition($comp);

			} else {
				$cond = $this->buildCondition($exp);
				$logic = null;
				$exp = null;
			}

			$curGroup[] = $cond;
			if ($logic === 'and') {
				$this->_conditions[] = $curGroup;
				$curGroup = array();
			}
		}
		$this->_conditions[] = $curGroup;
	}

	public function __toString() {
		$ands = array();
		foreach ($this->_conditions as $conditionGroup) {
			$ors = array();
			foreach ($conditionGroup as $condition) {
				$ors[] = "$condition[name] $condition[op] $condition[val]";
			}
			$ands[] = implode(' OR ', $ors);
		}
		return implode(' AND ', $ands);
	}

	/**
	 * Determines whether or not the encapsulated expression evaluates to true for
	 * the given set of values.
	 *
	 * @param Array $values Set of substitution values.
	 * @return boolean
	 */
	public function isSatisfiedBy($values) {
		if (is_array($values)) {
			$values = new TemplateValues($values);
		}

		if (!($values instanceof TemplateValues)) {
			throw new Exception("Given values must be either an array or a " .
				"TemplateValues instance.");
		}

		foreach ($this->_conditions as $group) {
			$groupSatisfied = false;
			foreach ($group as $cond) {
				$val = $values->getValue($cond['name']);

				if ($cond['val'] === null) {
					// Note the use of weak equality operator here.
					if ($val == true) {
						$groupSatisfied = true;
						break;
					}
				} else {
					$fn = self::$opEvaluators[$cond['op']];
					if ($fn($val, $cond['val'])) {
						$groupSatisfied = true;
						return true;
					}
				}
			}

			if (!$groupSatisfied) {
				return false;
			}
		}

		return true;
	}

	private function buildCondition($exp) {
		$ops = self::$ops;

		$matches = array();
		if (preg_match("/\s*(.+)\s*($ops)\s*(.*)\s*/", $exp, $matches)) {
			$name = trim($matches[1]);
			$op = trim($matches[2]);
			$val = trim($matches[3]);
		} else {
			$name = $exp;
			$op = null;
			$val = null;
		}

		if (is_numeric($val)) {
			$val = (float) $val;

			if ((int) $val == $val) {
				$val = (int) $val;
			}
		}

		return array(
			'name' => $name,
			'op'	 => $op,
			'val'  => $val
		);
	}

}
