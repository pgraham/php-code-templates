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
 * This class parses a code block for substitution tags.
 *
 * @author Philip Graham
 */
class CodeBlockParser {

  const DONE_REGEX     = '/^[\t ]*\$\{done\}/';
  const EACH_REGEX     = '/^([\t ]*)\$\{each:([^\}]+)\}/';
  const ELSE_REGEX     = '/^([\t ]*)\$\{else\}$/';
  const ELSEIF_REGEX   = '/^([\t ]*)\$\{elseif:([^\}]+)\}$/';
  const FI_REGEX       = '/^([\t ]*)\$\{fi\}$/';
  const IF_REGEX       = '/^([\t ]*)\$\{if:([^\}]+)\}$/';
  const JOIN_REGEX     = '/\$\{join:([^:]+):([^\}]+)\}/';
  const JSON_REGEX     = '/\$\{json:([^\}]+)\}/';
  const TAG_REGEX      = '/\$\{([^\}]+)}/';

  private static $_NUM_IFS = 0;

  /**
   * Parse a given piece of code for template elements and store the results in
   * the given CodeBlock implementation.
   *
   * @param string $code The code to parse
   * @param CodeBlock $block The block of code to populate with the parsed code
   * @return string The original code with nested blocks replaced with simple
   *   substitution tags.
   */
  public static function parse($code, CodeBlock $block) {
    // Parse blocks.  All block parsing is combined into a single loop so that
    // nested blocks are not parsed.  This is done by parsing the file one line
    // at a time and only parsing one level deep.
    $lines = explode("\n", $code);
    $parsedLines = Array();

    $curBlock = null;
    $curClause = null;
    $curCode = Array();
    $nestedLevel = 0;
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
        } else if (preg_match(self::EACH_REGEX, $line, $eachParams)) {
          $indent = $eachParams[1];
          $expression = $eachParams[2];

          $curBlock = new EachBlock($indent, $expression);
          $block->addEach($curBlock);

          $eachTag = $curBlock->getTag();
          $tagLine = $indent . str_replace($eachParams[0], $eachTag, $line);
          $parsedLines[] = $tagLine;
        } else {
          $parsedLines[] = $line;
        }

      } else if ($curBlock instanceof \reed\generator\IfBlock) {

        if (preg_match(self::IF_REGEX, $line, $ifParams)) {
          // There is a nested if, so no clauses for the current block should
          // be parsed until the nested if is closed
          $nestedLevel++;
          $curCode[] = $line;

        } else if (preg_match(self::ELSEIF_REGEX, $line, $ifParams)) {
          if ($nestedLevel === 0) {
            $curClause->setCode(implode("\n", $curCode));

            $curClause = new ElseIfClause($ifParams[2], $indent);
            $curBlock->addElseIf($curClause);
            $curCode = Array();

          } else {
            $curCode[] = $line;
          }

        } else if (preg_match(self::ELSE_REGEX, $line, $ifParams)) {
          if ($nestedLevel === 0) {
            $curClause->setCode(implode("\n", $curCode));

            $curClause = new ElseClause($indent);
            $curBlock->setElse($curClause);
            $curCode = Array();

          } else {
            $curCode[] = $line;
          }

        } else if (preg_match(self::FI_REGEX, $line, $ifParams)) {
          if ($nestedLevel === 0) {
            $curClause->setCode(implode("\n", $curCode));

            $curBlock = null;
            $curClause = null;
            $curCode = Array();

          } else {
            $nestedLevel--;
            $curCode[] = $line;
          }

        } else {
          $curCode[] = $line;
        }

      } else if ($curBlock instanceof \reed\generator\EachBlock) {

        if (preg_match(self::EACH_REGEX, $line, $ifParams)) {
          // There is a nested if, so no clauses for the current block should
          // be parsed until the nested if is closed
          $nestedLevel++;
          $curCode[] = $line;

        } else if (preg_match(self::DONE_REGEX, $line)) {
          if ($nestedLevel === 0) {
            $curBlock->setCode(implode("\n", $curCode));

            $curBlock = null;
            $curCode = Array();

          } else {
            $nestedLevel--;
            $curCode[] = $line;
          }

        } else {
          $curCode[] = $line;
        }

      }
    }

    $code = implode("\n", $parsedLines);

    // Get joins
    $joins = Array();
    preg_match_all(self::JOIN_REGEX, $code, $joins, PREG_SET_ORDER);
    foreach ($joins AS $join) {
      $name = $join[1];
      $glue = $join[2];
      $block->addJoin($name, $glue);
    }

    // Get JSON outputs
    $jsons = Array();
    preg_match_all(self::JSON_REGEX, $code, $jsons, PREG_SET_ORDER);
    foreach ($jsons AS $json) {
      $name = $json[1];
      $block->addJson($name);
    }

    $tags = Array();
    preg_match_all(self::TAG_REGEX, $code, $tags, PREG_SET_ORDER);
    foreach ($tags AS $tag) {
      if (preg_match('/^\$\{if\d+\}$/', $tag[0])) {
        continue;
      }

      if (preg_match('/^\$\{each\d+\}$/', $tag[0])) {
        continue;
      }

      if (substr($tag[1], 0, 5) === 'join:') {
        continue;
      }

      if (substr($tag[1], 0, 5) === 'json:') {
        continue;
      }

      $block->addTag($tag[1]);
    }

    return $code;
  }
}
