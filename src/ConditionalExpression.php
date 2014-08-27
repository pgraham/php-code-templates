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
use Exception;

/**
 * This class encapsulates expression evaluation for a conditional clause of a
 * {@link ConditionalBlock}.
 *
 * NOTE: There is a known issue that using any of the operators, or the words
 *       'and' or 'or' as comparison values will not behave as expected.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ConditionalExpression
{

	const NAME_RE = '/(\w+)(?:\[([\w[\]]+)\])*/';
	const COND_RE = '/\s*([\w[\]]+|\d+|\'.*\'|".*")(?:\s+(?:(?:(=|>|>=|<|<=|!=)\s+([\w[\]]+|\d+|\'.*?\'|".*?"))|(ISSET|ISNOTSET))(?:\s+(or|and)\s+(.+))?)?/i';

	/* Whether or not the class has been statically constructed. */
	private static $initialized = false;

	/* Regular expression for parsing a condition */
	private static $COND_RE;

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
		if (!self::$initialized) {
			self::initialize();
		}
		return array_key_exists($op, self::$opEvaluators);
	}

	/*
	 * ===========================================================================
	 * Instance
	 * ===========================================================================
	 */

	private $conditions = [];

	/**
	 * Create a new ConditionalExpression.
	 *
	 * @param string $expression Unparsed expression string.
	 */
	public function __construct($expression) {
		if (!self::$initialized) {
			self::initialize();
		}

		$curGroup = [];

		$exp = $expression;
		while ($exp !== null) {
			$matches = [];
			if (preg_match(self::COND_RE, $exp, $matches)) {
				$lhs = $matches[1];
				$op = null;
				$rhs = null;
				if (
					isset($matches[2]) && $matches[2] !== '' &&
					isset($matches[3]) && $matches[3] !== ''
				) {
					// Binary operator
					$op = $matches[2];
					$rhs = $matches[3];
					if (!$rhs) {
					}
				} else if (!empty($matches[4])) {
					// Unary operator
					$op = $matches[4];
				}
				$op = strtoupper($op);

				if (!empty($matches[5])) {
					$logic = strtolower($matches[5]);
					$exp = $matches[6];
				} else {
					// No more expressions to parse
					$logic = null;
					$exp = null;
				}

				$curGroup[] = $this->buildCondition($lhs, $op, $rhs);
				if ($logic === 'and') {
					$this->conditions[] = $curGroup;
					$curGroup = [];
				}
			} else {
				throw new InvalidConditionalExpressionException($expression);
			}
		}
		$this->conditions[] = $curGroup;
	}

	public function __toString() {
		$ands = [];
		foreach ($this->conditions as $conditionGroup) {
			$ors = [];
			foreach ($conditionGroup as $condition) {
				if ($condition['lhs']['type'] === 'var') {
					$or = (string) $condition['lhs']['val'];
				} else {
					$or = $condition['lhs']['val'];
				}

				if ($condition['op']) {
					$or .= " $condition[op]";
					if ($condition['rhs'] !== null) {
						if ($condition['rhs']['type'] === 'var') {
							$or .= (string) $condition['rhs']['val'];
						} else {
							$or .= $condition['rhs']['val'];
						}
					}
				}
				$ors[] = $or;
			}
			$ands[] = implode(' OR ', $ors);
		}
		return implode(' AND ', $ands);
	}

	/**
	 * Determines whether or not the encapsulated expression evaluates to true for
	 * the given set of values.
	 *
	 * @param array $values Set of substitution values.
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

		foreach ($this->conditions as $group) {
			$groupSatisfied = false;
			foreach ($group as $cond) {
				if ($cond['lhs']['type'] === 'var') {
					$lhs = $values->getValue(
						$cond['lhs']['val']->getName(),
						$cond['lhs']['val']->getIndexes()
					);
				} else {
					$lhs = $cond['lhs']['val'];
				}

				$rhs = null;
				if ($cond['rhs']) {
					if ($cond['rhs']['type'] === 'var') {
						$rhs = $values->getValue(
							$cond['rhs']['val']->getName(),
							$cond['rhs']['val']->getIndexes()
						);
					} else {
						$rhs = $cond['rhs']['val'];
					}
				}

				if ($cond['op']) {
					$fn = self::$opEvaluators[$cond['op']];
					if ($fn($lhs, $rhs)) {
						$groupSatisfied = true;
						return true;
					}
				} else {
					if ($lhs == true) {
						$groupSatisfied = true;
						break;
					}
				}
			}

			if (!$groupSatisfied) {
				return false;
			}
		}

		return true;
	}

	private function buildCondition($lhs, $op, $rhs) {
		$lhs = $this->parseOperand($lhs);
		if ($rhs !== '' && $rhs !== null) {
			$rhs = $this->parseOperand($rhs);
		}

		return [ 'lhs' => $lhs, 'op' => $op, 'rhs' => $rhs ];
	}

	private function parseOperand($val) {
		if (is_numeric($val)) {
			$type = 'number';
			$val = (float) $val;

			if ((int) $val == $val) {
				$val = (int) $val;
			}
		} else if (preg_match('/^(\'|")(.+)\1$/', $val, $matches)) {
			$type = 'string';
			$val = $matches[2];
		} else if ($varName = VariableNameParser::parse($val)) {
			$type = 'var';
			$val = $varName;
		} else {
			// TODO Invalid operand
			// throw new InvalidOperandException($val);
		}

		return [ 'type' => $type, 'val' => $val ];
	}

}
