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
 * CompositeBlock that is output conditionally.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ConditionalBlock extends CompositeBlock {

  private $_expression;
  private $_else;

  public function __construct($expression, $lineNum) {
    parent::__construct($lineNum);
    if ($expression !== null) {
      $this->_expression = new ConditionalExpression($expression);
    }
  }

  public function forValues(TemplateValues $values) {
    if ($this->_expression === null ||
        $this->_expression->isSatisfiedBy($values))
    {
      return parent::forValues($values);
    } else if ($this->_else !== null) {
      return $this->_else->forValues($values);
    } else {
      return null;
    }
  }

  public function getElse() {
    return $this->_else;
  }

  public function getExpression() {
    return $this->_expression;
  }

  public function setElse(ConditionalBlock $else) {
    $this->_else = $else;
  }
}
