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

/**
 * Interface for classes that providing naming for actors.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
interface ActorNamingStrategyInterface
{

    /**
     * Get the basename of any aspect of the specified target class. Aspects
     * which provide different functionality for the same target class will all
     * have the same base name and will be differentiated by their namespace.
     *
     * @param string $targetClass
     */
    public function getActorName($targetClass);
    
}
