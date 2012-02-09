<?php
/**
 * =============================================================================
 * Copyright (c) 2011, Philip Graham
 * All rights reserved.
 *
 * This file is part of Clarinet and is licensed by the Copyright holder under
 * the 3-clause BSD License.  The full text of the license can be found in the
 * LICENSE.txt file included in the root directory of this distribution or at
 * the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace reed\generator;

/** 
 * This class parses a code template for if blocks.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class IfParser {

  private static $_NUM_IFS = 0;

  const ELSE_REGEX     = '/^([\t ]*)\$\{else\}$/';

  const ELSEIF_REGEX   = '/^([\t ]*)\$\{elseif:([^\}]+)\}$/';

  const FI_REGEX       = '/^([\t ]*)\$\{fi\}$/';

  const IF_REGEX       = '/^([\t ]*)\$\{if:([^\}]+)\}$/';

  /**
   * Parse the given code for if blocks and populate the CodeTemplate with a
   * matching object representation.
   *
   * @param string $code The code template parse.
   * @param CodeBlock $block The object to populate.
   * @return The code template with if blocks replaced by shorter tags for
   *   substitution.
   */
  public static function parse($code, CodeBlock $block) {
    $lines = explode("\n", $code);
    $parsedLines = Array();

    $curBlock = null;
    $curClause = null;
    $curCode = Array();
    foreach ($lines AS $line) {
      $ifParams = Array();

      if ($curBlock === null) {

        if (preg_match(self::IF_REGEX, $line, $ifParams)) {
          $ifNum = ++self::$_NUM_IFS;
          $indent = $ifParams[1];
          $expression = $ifParams[2];

          $curBlock = new IfBlock($ifNum);
          $block->addIf($curBlock);

          $curClause = new IfClause($expression, $indent);
          $curBlock->setIf($curClause);

          $parsedLines[] = "$indent\${if{$ifNum}}";
        } else {
          $parsedLines[] = $line;
        }

      } else if (preg_match(self::ELSEIF_REGEX, $line, $ifParams)) {
        $curClause->setCode(implode("\n", $curCode));

        $curClause = new ElseIfClause($ifParams[2], $indent);
        $curBlock->addElseIf($curClause);
        $curCode = Array();

      } else if (preg_match(self::ELSE_REGEX, $line, $ifParams)) {
        $curClause->setCode(implode("\n", $curCode));

        $curClause = new ElseClause($indent);
        $curBlock->setElse($curClause);
        $curCode = Array();

      } else if (preg_match(self::FI_REGEX, $line, $ifParams)) {
        $curClause->setCode(implode("\n", $curCode));

        $curBlock = null;
        $curClause = null;
        $curCode = Array();

      } else {
        $curCode[] = $line;
      }
    }

    return implode("\n", $parsedLines);
  }
}
