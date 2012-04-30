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

  private $_outputPath;

  /**
   * Create a new generator that outputs to the given path.
   *
   * @param string $outputPath The path for where to output the code.  This
   *   path must be writable by the current user.
   */
  public function __construct($outputPath) {
    $this->_outputPath = $outputPath;
    if (substr($this->_outputPath, -1) == '/') {
      $this->_outputPath = substr($this->_outputPath, 0, -1);
    }
  }

  /**
   * Generate the code.  This method delegates to the implementation for the
   * acutal generation then outputs to the specified path.
   *
   * @param string $className The entity for which to generate code.
   */
  public function generate($className) {
    $classBody = $this->_generate($className);

    $fileName = str_replace('\\', '/', $className) . '.php';

    $fullPath = $this->_outputPath . '/' . $fileName;
    if (!file_exists(dirname($fullPath))) {
      mkdir(dirname($fullPath), 0755, true);
    }
    $file = new SplFileObject($fullPath, 'w');
    $file->fwrite($classBody);
  }

  /**
   * This method is responsible for actually generating the actor code.
   *
   * @param string $className The name of the definition class.
   * @return string The PHP code for the generated actor.
   */
  protected abstract function _generate($className);
}
