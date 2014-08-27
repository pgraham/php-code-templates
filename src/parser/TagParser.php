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

use zpt\pct\MissingFilterExpressionArgumentException;
use zpt\pct\Substitution;
use zpt\pct\UnexpectedSubstitutionValueTypeException;

/**
 * Parse substitution tags from a single line of a template.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class TagParser {

	const TAG_RE = '/\/\*#\s*(\S+)\s*#?\*\//';

	const EXPR_RE = '/(?:(.+):)?(\w+)(?:\[([\w[\]]+)\])*/';

	const FLTR_RE = '/(\w+)(?:\((.+)\))?/';

	private $filters = [];

	public function __construct() {
		// Register set of built-in filters.

		// Default filter, does nothing to the given value
		$this->registerFilter('', function ($val) { return $val; });

		// Register filter that applies another filter to each element of an array
		// and returns the results as an array.
		$this->registerFilter('each', function ($val, $params) {
			// TODO Check for filter parameter
			// TODO Check that value is an array
			$fn = $this->filters[$params[0]];
			$filtered = [];
			foreach ($val as $k => $v) {
				$filtered[$k] = $fn($v);
			}
			return $filtered;
		});

		$this->registerFilter('json', function ($val) {
			return json_encode($val);
		});

		$this->registerFilter('join', function ($val, $params) {
			if (!is_array($val)) {
				throw new UnexpectedSubstitutionValueTypeException('array', $val);
			}

			if (!isset($params[0])) {
				throw new MissingFilterExpressionArgumentException([ 'glue' ]);
			}

			// TODO Check for glue parameter
			// TODO Check that value is an array
			return implode($params[0], $val);
		});

		$this->registerFilter('php', function ($val) {
			$export = function ($val) use (&$export) {
				if (is_array($val)) {
						$vals = array();
						foreach ($val as $k => $v) {
								$vals[] = $export($k, true)
												. ' => '
												. $export($v);
						}
						return 'array(' . implode(',', $vals) . ')';

				} elseif (is_object($val) && get_class($val) === 'stdClass') {
						return '(object) ' . $export((array) $val);

				} else {
						return var_export($val, true);
				}
			};
			return $export($val);
		});

		$this->registerFilter('xml', function ($val) {
			return htmlspecialchars($val, ENT_QUOTES | ENT_XML1, 'UTF-8', false);
		});
	}

	public function registerFilter($name, $fn) {
		$this->filters[$name] = $fn;
	}

	public function parse($line) {
		$tags = [];

		$matches = [];
		if (preg_match_all(self::TAG_RE, $line, $matches)) {
			foreach ($matches[0] as $idx => $tagKey) {
				$tags[$tagKey] = $this->parseTag($matches[1][$idx]);
			}
		}

		return $tags;
	}

	private function parseTag($tag) {
		$matches = [];

		if (preg_match(self::EXPR_RE, $tag, $matches)) {
			$name = $matches[2];

			$filters = [];
			if ($matches[1]) {
				$filtersExps = explode('-', $matches[1]);
				foreach ($filtersExps as $filterExp) {
					if (preg_match(self::FLTR_RE, $filterExp, $m)) {
						$filters[] = [
							$this->filters[$m[1]],
							isset($m[2]) ? $m[2] : []
						];
					} else {
						// TODO Invalid filter expression
					}
				}
			}

			$indexes = [];
			if (isset($matches[3])) {
				$indexes = explode('][', $matches[3]);
			}

			return new Substitution($name, $indexes, $filters);
		}
	}
}
