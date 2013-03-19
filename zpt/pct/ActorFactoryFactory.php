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
    private $injectors = array();
    private $namingStrategy;

    public function getFactory($baseNs)
    {
        if (!array_key_exists($baseNs, $this->factories)) {
            $factory = new ActorFactory($baseNs);
            $factory->setNamingStrategy($this->namingStrategy);

            if (isset($this->injectors[$baseNs])) {
                foreach ($this->injectors[$baseNs] as $injector) {
                    $factory->registerInjector($injector);
                }
            }

            $this->factories[$baseNs] = $factory;
        }
        return $this->factories[$baseNs];
    }

    public function registerInjector($baseNs, ActorInjector $injector)
    {
        if (!isset($this->injectors[$baseNs])) {
            $this->injectors[$baseNs] = array();
        }

        $this->injectors[$baseNs][] = $injector;

        if (isset($this->factories[$baseNs])) {
            $factory = $this->factories[$baseNs];
            $factory->registerInjector($injector);
        }
    }

    public function setNamingStrategy(
        ActorNamingStrategyInterface $namingStrategy
    ) {
        $this->namingStrategy = $namingStrategy;
    }
}
