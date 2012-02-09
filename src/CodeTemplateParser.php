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
namespace pct;

/**
 * This class parses a code template into a object structure appropriate for
 * substitution.
 *
 * @author Philip Graham
 */
class CodeTemplateParser {

  /**
   * Parse the given code and populate the given CodeTemplate.
   *
   * @param string $code The code to parse.
   * @param CodeTemplate $template The template to populate.
   */
  public function parse($code) {
    $template = new CodeTemplate();
    $template->setCode($code);
    return $template;
  }
}
