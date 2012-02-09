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

use \Exception;

/**
 * This class parses the ${each...} substitution tags from a code template and
 * populates the given code template.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class EachParser {

  const DONE_REGEX     = '/^[\t ]*\$\{done\}/';
  const EACH_REGEX     = '/^([\t ]*)\$\{each:([^\}]+)\}/';

  /**
   * Parse the given code template for each substitutions and populate the given
   * {@link CodeTemplate}.
   *
   * @param string $code The code template to parse
   * @param CodeBlock $block The object to populate with each substitution
   *   information.
   * @return string Modified code to be passed into the given CodeTemplate's
   *   forValues(...) method.
   */
  public static function parse($code, CodeBlock $block) {
    $lines = explode("\n", $code);
    $parsedLines = Array();

    $curBlock = null;
    $curCode = Array();
    $lineNum = 0;
    foreach ($lines AS $line) {
      $lineNum++;
      try {
        $eachParams = Array();

        if ($curBlock === null) {

          if (preg_match(self::EACH_REGEX, $line, $eachParams)) {
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

        } else if (preg_match(self::DONE_REGEX, $line)) {
          $curBlock->setCode(implode("\n", $curCode));

          $curBlock = null;
          $curCode = Array();

        } else {
          $curCode[] = $line;
        }
      } catch (Exception $e) {
        throw new Exception("Error parsing line $lineNum:\n\n$line\n\n", $e);
      }
    }

    return implode("\n", $parsedLines);
  }
}
