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
 * This class encapsulates an if-block in a code template.  An if-block
 * consists of an 'if' clause, zero or more 'elseif' clauses and an optional
 * 'else' clause.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class IfBlock {

  /* The if-block's else clause */
  private $_else;

  /* THe if-block's else if clauses */
  private $_elseifs = Array();

  /* The id for this if block */
  private $_id;

  /* The if-block's opening if clause */
  private $_if;

  /**
   * Create a new if-block.
   *
   * @param mixed $id The id of this if block.  This should be unique among all
   *   IfBlocks for a particular template.
   */
  public function __construct($id) {
    $this->_id = $id;
  }

  /**
   * And ElseIf clause to the if block.
   *
   * @param ElseIfClause $elseif
   */
  public function addElseIf(ElseIfClause $elseif) {
    $this->_elseifs[] = $elseif;
  }

  /**
   * Get the block of code that should be substituted for the given set of
   * substitution values.
   *
   * @param Array $values
   * @return The code block for the first clause who's expression is satisfied
   *   by the given values of an empty string no clauses are satified.  If an
   *   else clause has been set then it will always be satisfied.
   */
  public function forValues(Array $values) {
    if (isset($this->_if)) {
      if ($this->_if->isSatisfiedBy($values)) {
        return $this->_if->forValues($values);
      }
    }

    foreach ($this->_elseifs AS $elseif) {
      if ($elseif->isSatisfiedBy($values)) {
        return $elseif->forValues($values);
      }
    }

    if (isset($this->_else)) {
      return $this->_else->forValues($values);
    }

    return '';
  }

  /**
   * Getter for the IfBlock's id.
   *
   * @return mixed $id Whatever was passed into the constructor.
   */
  public function getId() {
    return $this->_id;
  }

  /**
   * Set the IfBlock's ElseClause.
   *
   * @param ElseClause $else
   */
  public function setElse(ElseClause $else) {
    $this->_else = $else;
  }

  /**
   * Set the IfBlock's IfClause.
   *
   * @param IfClause $ifClause
   */
  public function setIf(IfClause $if) {
    $this->_if = $if;
  }

}
