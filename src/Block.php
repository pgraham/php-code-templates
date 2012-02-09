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
 * Interface for block level elements in a parsed code template.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
interface Block {

  /**
   * This method is responsible for substituting the given values into the
   * block and returning the result
   *
   * @param array $values
   * @return string
   */
  public function forValues(array $values);
}
