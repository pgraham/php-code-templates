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
namespace zpt\pct;

/**
 * This class encapsulates a parsed code template.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class CodeTemplate extends CompositeBlock {

  public function __construct() {
    parent::__construct(1);
  }

  /**
   * Save the resolved with the given values at the given path.
   *
   * @param string $outPath
   * @param array $values
   */
  public function save($outPath, $values = null) {
    if (!file_exists(dirname($outPath))) {
      mkdir(dirname($outPath), 0755, true);
    }

    file_put_contents($outPath, $this->forValues($values));
  }
}
