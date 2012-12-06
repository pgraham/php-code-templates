<?php
/**
 * =============================================================================
 * Copyright (c) 2011, Philip Graham
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

use \SplFileObject;

/**
 * This class provides functionality for generating an actor using a template
 * based on some sort of definition class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
abstract class AbstractGenerator {

  /*
   * Namespace of generated classes.  Should be overridden by implementations.
   */
  protected static $actorNamespace = '';

  /* Cache of instantiated actors. */
  private static $_cache = array();

  /**
   * Retrieve an instance of the generated class for the given class definition.
   *
   * @param string $pageDef The name of the class that provides the definition
   *   for the generated class.
   */
  public static function get($defClass) {
    if (!array_key_exists($defClass, self::$_cache)) {
      $actor = str_replace('\\', '_', $defClass);
      $fq = static::$actorNamespace . "\\$actor";

      self::$_cache[$defClass] = new $fq();
    }

    return self::$_cache[$defClass];
  }

  /*
   * ===========================================================================
   * Instance
   * ===========================================================================
   */

  private $_outputPath;

  private $_tmpl;

  /**
   * Create a new generator that outputs to the given path.  The given output
   * path is used as a base path.  The generation logic will append a PSR-0
   * compliant path to the specified path, taking into account both the value
   * of static::$actorNamespace and the name of the actor itself.
   *
   * # Example
   * For a class definition my\ns\Definition and the following
   * implementation:
   *
   *     class MyGenerator extends AbstractGenerator {
   *         protected static $actorNamespace = 'my\dynamic\ns';
   *
   *         public function __construct() {
   *             parent::__construct('/path/to/site/target');
   *         }
   *
   *         // ...
   *     }
   *
   * The actor will be output at /path/to/site/target/my/dynamic/ns/my/ns/Definition.php
   *
   * @param string $outputPath The path for where to output the code.  This
   *   path must be writable by the current user.
   */
  public function __construct($outputPath) {
    $this->_outputPath = rtrim($outputPath, '/');
    $this->_outputPath .= '/' . str_replace('\\', '/', static::$actorNamespace);

    $parser = new CodeTemplateParser();
    $this->_tmpl = $parser->parse(file_get_contents($this->getTemplatePath()));
  }

  /**
   * Generate the code.  This method delegates to the implementation for the
   * acutal generation then outputs to the specified path.
   *
   * @param string $defClass The definition for which to generate an actor.
   */
  public function generate($defClass) {
    $values = $this->getValues($defClass);
    $values['actorNs'] = static::$actorNamespace;
    $values['actorClass'] = str_replace('\\', '_', $defClass);
    $values['model'] = $defClass;
    $resolved = $this->_tmpl->forValues($values);

    $fileName = str_replace(array('\\', '_'), '/', $defClass) . '.php';

    $fullPath = $this->_outputPath . '/' . $fileName;

    $dirPath = dirname($fullPath);
    if (!file_exists($dirPath)) {
      mkdir($dirPath, 0755, true);
    }

    $file = new SplFileObject($fullPath, 'w');
    $file->fwrite($resolved);
  }

  public function getActorClassName($defClass) {
    return static::$actorNamespace . '\\' . str_replace('\\', '_', $defClass);
  }

  /**
   * This method is responsible for returning the path to the template that is
   * used to generate the actor.
   *
   * @return string
   */
  protected abstract function getTemplatePath();

  /**
   * This method is responsible for actually generating the substitution values
   * for generating the actor for the specified definition class.  These values
   * will be substituted into the template specified by getTemplatePath().
   *
   * @param string $className The name of the definition class.
   * @return array Substitution values for generating the actor.
   */
  protected abstract function getValues($className);

}
