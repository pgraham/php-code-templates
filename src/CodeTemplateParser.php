<?php
/**
 * =============================================================================
 * Copyright (c) 2012, Philip Graham
 * All rights reserved.
 *
 * This file is part of php-code-templates and is licensed by the Copyright
 * holder under the 3-clause BSD License.  The full text of the license can be
 * found in the LICENSE.txt file included in the root directory of this
 * distribution or at the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace pct;

/**
 * This class parses a code template into a object structure appropriate for
 * substitution.
 *
 * @author Philip Graham
 */
class CodeTemplateParser {

  const DONE_RE   = '/^[\t ]*\$\{done\}/';
  const EACH_RE   = '/^[\t ]*\$\{each:([^\}]+)\}/';
  const ELSE_RE   = '/^[\t ]*\$\{else\}$/';
  const ELSEIF_RE = '/^[\t ]*\$\{elseif:([^\}]+)\}$/';
  const FI_RE     = '/^[\t ]*\$\{fi\}$/';
  const IF_RE     = '/^[\t ]*\$\{if:([^\}]+)\}$/';

  /* String that constitutes a level of indentation in the template */
  private $_indentString = '  ';

  /**
   * Parse the given code and populate the given CodeTemplate.
   *
   * @param string $code The code to parse.
   * @param CodeTemplate $template The template to populate.
   */
  public function parse($code) {
    $template = new CodeTemplate();

    $lines = explode("\n", $code);

    // Current nested chain of CompositeBlocks
    $blockStack = array( $template );

    // The current CodeBlock to which CodeLines are being added.
    $curBlock = null;

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

      } else if (preg_match(self::FI_RE, $line)) {
        array_pop($blockStack);
        $curBlock = null;

      } else if (preg_match(self::EACH_RE, $line, $matches)) {
        $eachBlock = new EachBlock($matches[1], $lineNum);

        $headBlock = end($blockStack);
        $headBlock->addBlock($eachBlock);
        array_push($blockStack, $eachBlock);

        $curBlock = null;

      } else if (preg_match(self::DONE_RE, $line)) {
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

        $indent = $this->_parseIndent($line) - count($blockStack) + 1;
        $codeLine->setIndent($indent);

        $curBlock->addLine($codeLine);
      }
    }

    return $template;
  }

  public function setIndentString($indentString) {
    $this->_indentString = $indentString;
  }

  private function _parseIndent($line) {
    $indentRe = '/^' . preg_quote($this->_indentString) . '/';

    $indent = 0;
    while (preg_match($indentRe, $line)) {
      $indent++;
      $line = preg_replace($indentRe, '', $line);
    }
    return $indent;
  }

}
