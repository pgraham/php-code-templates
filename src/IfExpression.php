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
 * This class encapsulates expression evaluation for a conditional clause of an
 * IfBlock.
 *
 * TODO - Abstract expression evaluation then have this class consume a set of
 *        supported operators
 *      - Add support for and boolean operators.  Bracketing will be implied,
 *        ORs will be grouped and separated by ANDs.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class IfExpression {

  /* Whether or not the class has been statically constructed. */
  private static $_initialized = false;

  /*
   * Non-capturing regular expression for all supported operators.  Any
   * strings matched by this regexp must have a matching entry in opEvaluators.
   */
  private static $_ops = '(?:=|>|>=|<|<=|!=|ISSET|ISNOTSET)';

  private static $_opEvaluators;

  /*
   * Static constructor.  Will happen the first time an instance of this class
   * is created.
   */
  private static function _initialize() {
    self::$_initialized = true;

    self::$_opEvaluators = array(
      // '=' Evaluator
      '=' => function ($a, $b) {
        return $a === $b;
      },

      // '>' Evaluator
      '>' => function ($a, $b) {
        return $a > $b;
      },

      // '>=' Evaluator
      '>=' => function ($a, $b) {
        return $a >= $b;
      },

      // '<' Evaluator
      '<' => function ($a, $b) {
        return $a < $b;
      },

      // '<=' Evaluator
      '<=' => function ($a, $b) {
        return $a <= $b;
      },

      // '!=' Evaluator
      '!=' => function ($a, $b) {
        return $a !== $b;
      },

      'ISSET' => function ($a, $b) {
        return $a !== null;
      },

      'ISNOTSET' => function ($a, $b) {
        return $a === null;
      }
    );
  }

  /*
   * ===========================================================================
   * Instance
   * ===========================================================================
   */

  private $_conditions = array();

  /**
   * Create a new IfExpression.
   *
   * @param string $expression Unparsed expression string.
   */
  public function __construct($expression) {
    if (!self::$_initialized) {
      self::_initialize();
    }

    $ops = self::$_ops;
    $varRe = '[[:alnum:]_-]+(?:\[[[:alnum:]_-]+\])?';
    $orRe = "\s*($varRe\s*(?:$ops\s*$varRe)?)\s*or\s*(.*)\s*";

    $exp = $expression;
    while ($exp !== null) {
      $matches = array();
      if (preg_match("/$orRe/", $exp, $matches)) {
        $comp = trim($matches[1]);
        $exp = trim($matches[2]);

        $this->_buildCondition($comp);

      } else {
        $this->_buildCondition($exp);
        $exp = null;
      }

    }
  }

  private function _buildCondition($exp) {
    $ops = self::$_ops;

    $matches = array();
    if (preg_match("/\s*(.+)\s*($ops)\s*(.*)\s*/", $exp, $matches)) {
      $name = trim($matches[1]);
      $op = trim($matches[2]);
      $val = trim($matches[3]);
    } else {
      $name = $exp;
      $op = null;
      $val = null;
    }

    $matches = array();
    if (preg_match('/([[:alnum:]_-]+)\[([[:alnum:]_-]+)\]/', $name, $matches)) {
      $name = array($matches[1], $matches[2]);
    }

    $this->_conditions[] = array(
      'name' => $name,
      'op'   => $op,
      'val'  => $val
    );
  }

  /**
   * Determines whether or not the encapsulated expression evaluates to true for
   * the given set of values.
   *
   * @param Array $values Set of substitution values.
   * @return boolean
   */
  public function isSatisfiedBy(array $values) {
    foreach ($this->_conditions AS $cond) {
      $val = $this->_extractValue($cond['name'], $values);

      if ($cond['val'] === null) {
        if ($val === true) {
          return true;
        }
      } else {
        $fn = self::$_opEvaluators[$cond['op']];
        if ($fn($val, $cond['val'])) {
          return true;
        }
      }
    }

    return false;
  }

  private function _extractValue($name, array $values) {
    $val = null;
    if (is_array($name)) {
      if (!isset($values[$name[0]]) ||
          !is_array($values[$name[0]]) ||
          !isset($values[$name[0]][$name[1]]))
      {
        return null;
      }

      $val = $values[$name[0]][$name[1]];
    } else {
      if (!isset($values[$name])) {
        return null;
      }

      $val = $values[$name];
    }

    return $val;
  }
}
