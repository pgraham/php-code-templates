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
 * This class encapsulates a line of code to be output when performing value
 * substitution on a template.	Actual output and number of times it is output
 * depends on the given substitution values and where in the template block
 * structure it appears.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class CodeLine
{

	private $indent = 0;
	private $line;
	private $lineNum;

	private $tags;

	/**
	 * Constructor.
	 *
	 * @param array $tags
	 *   Parsed substitution tags for thr line
	 * @param string $line
	 *   The ensupsulated line of code.
	 * @param int $lineNum
	 *   The line number on which this line of code appears
	 *   in it's source file.
	 */
	public function __construct(array $tags, $line, $lineNum)
	{
		$this->tags = $tags;
		$this->line = trim($line);
		$this->lineNum = $lineNum;
	}

	/**
	 * Substitute the given values into the template.
	 *
	 * @param TemplateValues $values
	 * @return string
	 */
	public function forValues(TemplateValues $values)
	{
		$search = array();
		$replace = array();
		foreach ($this->tags as $key => $tag) {
			try {
				$search[] = $key;
				$replace[] = $tag->getValue($values);
			} catch (UndefinedValueException $e) {
				throw new SubstitutionException($this->lineNum, $e);
			}
		}

		$r = $this->getIndent() . str_replace($search, $replace, $this->line);
		return $r;
	}

	public function getLineNum()
	{
		return $this->lineNum;
	}

	public function setIndent($indent)
	{
		$this->indent = $indent;
	}

	private function getIndent()
	{
		$indent = '';
		for ($i = 0; $i < $this->indent; $i++) {
			$indent .= '  ';
		}
		return $indent;
	}
}
