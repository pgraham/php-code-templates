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
  const EACH_RE   = '/^([\t ]*)\$\{each:([^\}]+)\}/';
  const ELSE_RE   = '/^([\t ]*)\$\{else\}$/';
  const ELSEIF_RE = '/^([\t ]*)\$\{elseif:([^\}]+)\}$/';
  const FI_RE     = '/^([\t ]*)\$\{fi\}$/';
  const IF_RE     = '/^([\t ]*)\$\{if:([^\}]+)\}$/';

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

    $lineNum = 1;
    foreach ($lines AS $line) {

      if (preg_match(self::IF_RE, $line)) {
      } else if (preg_match(self::ELSEIF_RE, $line)) {
      } else if (preg_match(self::ELSE_RE, $line)) {
      } else if (preg_match(self::FI_RE, $line)) {
      } else if (preg_match(self::EACH_RE, $line)) {
      } else if (preg_match(self::DONE_RE, $line)) {
      } else {
        if ($curBlock === null) {
          $curBlock = new CodeBlock();

          // Add the new code block to the head of block stack
          $headBlock = end($blockStack);
          $headBlock->addBlock($curBlock);
        }

        $codeLine = new CodeLine($line, $lineNum);
        $curBlock->addLine($codeLine);
      }

      $lineNum++;
    }

    return $template;
  }
}
