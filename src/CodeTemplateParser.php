<?php
/*
 * Copyright (c) 2012 - 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of php-code-templates. For the full copyright and license
 * information please view the LICENSE file that was distributed with this
 * source code.
 */
namespace zpt\pct;

use zpt\pct\exception\ParseException;
use zpt\pct\exception\StructureException;

/**
 * This class parses a code template into a object structure appropriate for
 * substitution.
 *
 * @author Philip Graham
 */
class CodeTemplateParser {

	const IF_RE      = '/^\s*#\{\s*if\s+(.+)$/';
	const ELSEIF_RE  = '/^\s*#\}?\{\s*elseif\s+(.+)$/';
	const ELSE_RE    = '/^\s*#\}?\{\s*else\s*$/';

	const SWITCH_RE  = '/^\s*#\{\s*switch\s+(.+)$/';
	const CASE_RE    = '/^\s*#\|\s*case\s+(.+)$/';
	const DEFAULT_RE = '/^\s*#\|\s*default\s*$/';

	const EACH_RE    = '/^\s*#\{\s*each\s+(.+)$/';

	const CLOSE_RE   = '/^\s*#\}\s*$/';

	/*
	 * String that constitutes a level of indentation in the template
	 * TODO Detect template indentation
	 */
	private $indentString = '  ';

	/**
	 * Parse the given code and populate the given CodeTemplate.
	 *
	 * @param string $code
	 *   Either the path to the file containing the code to
	 *   parse or the code to parse.
	 * @return CodeTemplate
	 */
	public function parse($code) {
		if (file_exists($code)) {
			$templatePath = $code;
			$code = file_get_contents($templatePath);
		} else {
			$templatePath = '-- CODE --';
		}

		$template = new CodeTemplate();

		$lines = explode("\n", $code);

		// Current nested chain of CompositeBlocks -- A quick trick block stack
		$blockStack = array( $template );

		// The current CodeBlock to which CodeLines are being added.
		$curBlock = null;

		try {
			$lineNum = 0;
			foreach ($lines AS $line) {
				$lineNum++;

				if (preg_match(self::IF_RE, $line, $matches)) {
					$ifBlock = new ConditionalBlock($matches[1], $lineNum);

					$headBlock = end($blockStack);
					$headBlock->addBlock($ifBlock);
					array_push($blockStack, $ifBlock);

					$curBlock = null;

				} else if (preg_match(self::ELSEIF_RE, $line, $matches)) {
					$elseIfBlock = new ConditionalBlock($matches[1], $lineNum);

					$headBlock = array_pop($blockStack);
					$headBlock->setElse($elseIfBlock);
					array_push($blockStack, $elseIfBlock);

					$curBlock = null;

				} else if (preg_match(self::ELSE_RE, $line)) {
					$elseBlock = new ConditionalBlock(null, $lineNum);

					$headBlock = array_pop($blockStack);
					$headBlock->setElse($elseBlock);
					array_push($blockStack, $elseBlock);

					$curBlock = null;

				} else if (preg_match(self::SWITCH_RE, $line, $matches)) {
					$switchBlock = new SwitchBlock($matches[1], $lineNum);

					$headBlock = end($blockStack);
					$headBlock->addBlock($switchBlock);
					array_push($blockStack, $switchBlock);

					$curBlock = null;

				} else if (preg_match(self::CASE_RE, $line, $matches)) {
					$headBlock = end($blockStack);
					if ($headBlock instanceof SwitchBlock) {
						$headBlock->addCase($matches[1], $lineNum);
						$curBlock = null;
					} else {
						$msg = "Case statements must appear within a switch block.";
						throw new StructureException($msg);
					}

				} else if (preg_match(self::DEFAULT_RE, $line)) {
					$headBlock = end($blockStack);
					if ($headBlock instanceof SwitchBlock) {
						$headBlock->setDefault($lineNum);
						$curBlock = null;
					} else {
						$msg = "Default statements must appear within a switch block.";
						throw new StructureException($msg);
					}

				} else if (preg_match(self::EACH_RE, $line, $matches)) {
					$eachBlock = new EachBlock($matches[1], $lineNum);

					$headBlock = end($blockStack);
					$headBlock->addBlock($eachBlock);
					array_push($blockStack, $eachBlock);

					$curBlock = null;

				} else if (preg_match(self::CLOSE_RE, $line)) {
					array_pop($blockStack);
					$curBlock = null;

				} else {
					if ($curBlock === null) {
						$curBlock = new CodeBlock();

						// Add the new code block to the head of block stack
						$headBlock = end($blockStack);
						$headBlock->addBlock($curBlock);
					}

					$codeLine = new CodeLine($line, $lineNum);

					$indent = $this->parseIndent($line) - count($blockStack) + 1;
					$codeLine->setIndent($indent);

					$curBlock->addLine($codeLine);
				}
			}

			if (count($blockStack) > 1) {
				$msg = "Unclosed template block";
				throw new StructureException($msg);
			}
		} catch (StructureException $e) {
			throw new ParseException($templatePath, $lineNum, $e);
		}

		return $template;
	}

	public function setIndentString($indentString) {
		$this->indentString = $indentString;
	}

	private function parseIndent($line) {
		$indentRe = '/^' . preg_quote($this->indentString) . '/';

		$indent = 0;
		while (preg_match($indentRe, $line)) {
			$indent++;
			$line = preg_replace($indentRe, '', $line);
		}
		return $indent;
	}

}
