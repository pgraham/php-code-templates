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
 * Base class for all Clode blocks clauses.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class CodeBlock implements Block {

  private $_lines = array();

  public function addLine(CodeLine $line) {
    $this->_lines[] = $line;
  }

  public function forValues($values) {
    $substituted = array();
    foreach ($this->_lines AS $line) {
      $substituted[] = $line->forValues($values);
    }
    return implode("\n", $substituted);
  }

}
