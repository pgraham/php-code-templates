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
namespace zpt\pct;

/**
 * This class encapsulates expression evaluation for a conditional clause of a
 * {@link ConditionalBlock}.
 *
 * TODO - Add support for 'and' boolean operators.  Bracketing will be implied,
 *        ORs will be grouped and separated by ANDs (Conjunctive normal form).
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ConditionalExpression {

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
   * Create a new ConditionalExpression.
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
  public function isSatisfiedBy(TemplateValues $values) {
    foreach ($this->_conditions AS $cond) {
      $val = $values->getValue($cond['name']);

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

}
