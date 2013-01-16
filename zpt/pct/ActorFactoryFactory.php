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
 * Abstract factory for actor factory instances.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ActorFactoryFactory
{

    private $factories = array();
    private $namingStrategy;

    public function getFactory($baseNs)
    {
        if (!array_key_exists($baseNs, $this->factories)) {
            $factory = new ActorFactory($baseNs);
            $factory->setNamingStrategy($this->namingStrategy);

            $this->factories[$baseNs] = $factory;
        }
        return $this->factories[$baseNs];
    }

    public function setNamingStrategy(
        ActorNamingStrategyInterface $namingStrategy
    ) {
        $this->namingStrategy = $namingStrategy;
    }
}
