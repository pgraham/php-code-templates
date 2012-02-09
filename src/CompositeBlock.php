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
 * Block implementation that consists solely of child Blocks.  Children can be
 * either CodeBlocks or CompositeBlocks.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
abstract class CompositeBlock implements Block {

  protected $_blocks = array();

  public function addBlock(Block $block) {
    $this->_blocks[] = $block;
  }
}
