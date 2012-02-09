<?php
/**
 * =============================================================================
 * Copyright (c) 2010, Philip Graham
 * All rights reserved.
 *
 * This file is part of php-code-templates and is licensed by the Copyright
 * holder under the 3-clause BSD License.  The full text of the license can be
 * found in the LICENSE.txt file included in the root directory of this
 * distribution or at the link below.
 * =============================================================================
 *
 * This file sets up the environment for running tests.
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

spl_autoload_register(function ($className) {
  if (substr($className, 0, 4) != "pct\\") {
    return;
  }

  $logicalPath = str_replace('\\', DIRECTORY_SEPARATOR, substr($className, 4));
  $fullPath = __DIR__ . "/../src/$logicalPath.php";

  if (file_exists($fullPath)) {
    require $fullPath;
  }
});
