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
 * This class represents an ${each:name as alias} ... ${done} substitution block
 * in a code template.
 *
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class EachBlock extends CompositeBlock {

  /* The name of the value as used in the code block */
  private $_alias;

  /* The name of the value to substitute into the block */
  private $_name;

  /**
   * Create a new each block representation.
   *
   * @param string $indent The amount of indentation for each substituted line.
   * @param string $expression The each expression.  Must in the form
   *   valueName as alias
   */
  public function __construct($expression, $lineNum) {
    parent::__construct($lineNum);

    $parts = preg_split('/ as /i', $expression, 2);
    if (count($parts) !== 2) {
      throw new SubstitutionException(
        'Each block expression must be in the form ${each:<array> as <var>}',
        $this->lineNum);
    }

    $this->_name = trim($parts[0]);
    $this->_alias = trim($parts[1]);
  }

  /**
   * Get the block of code that should be substituted for the given set of
   * substitution values.
   *
   * @param Array $values
   * @return string The resolved code block for the given substitution values.
   */
  public function forValues($values) {
    $itr = $values->getValue($this->_name);
    if ($itr === null) {
      throw new UndefinedValueException($this->_name, $this->lineNum);
    }

    if (!is_array($itr)) {
      throw new InvalidTypeException($this->_name, 'array', $itr,
        $this->lineNum);
    }

    $eaches = array();
    foreach ($itr as $val) {
      $values[$this->_alias] = $val;

      $eaches[] = parent::forValues($values);
      unset($values[$this->_alias]);
    }

    return implode("\n", $eaches);
  }

}
