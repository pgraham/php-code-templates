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
 * Base class for IfBlock clauses that are conditionally output based on the
 * substitution values passed to a CodeTemplate.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
abstract class ConditionalClause extends CodeBlock {

  /*
   * The expression that must be satisfied in order for this clauses code block
   * to be included in the resolved template.
   */
  protected $_expression;

  /**
   * Create a new IfClause.
   *
   * @param string $indent The indentation of the clause declaration.
   * @param string $expression Unparsed expression string.
   */
  public function __construct($expression, $indent) {
    parent::__construct($indent);

    $this->_expression = new IfExpression($expression);
  }

  /**
   * Determines whether or not this clause is satisfied by the given set of
   * substitution values.
   *
   * @param Array $values
   */
  public function isSatisfiedBy(Array $values) {
    return $this->_expression->isSatisfiedBy($values);
  }
}
