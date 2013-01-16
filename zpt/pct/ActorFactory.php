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
 * Factory class for Actor instances.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ActorFactory
{

    /* Cache of instantiated actors */
    private $actors = array();

    /* Whether or not to use the cache when retrieving aspects. */
    private $useCache = true;

    /* The base namespace for actors created by this factory. */
    private $baseNamespace;

    /* Naming strategy for instantiating actors. */
    private $namingStrategy;

    /**
     * Create a new factory for aspects that live in the given namespace.
     *
     * @param string $baseNamespace
     */
    public function __construct($baseNamespace)
    {
        $this->baseNamespace = $baseNamespace;
    }

    /**
     * Get the aspect for the given target class that lives in this factory's
     * base namespace.
     *
     * @param string $targetClass
     */
    public function get($targetClass)
    {
        if (!$this->useCache) {
          return $this->createActor($targetClass);
        }

        if (!array_key_exists($targetClass, $this->actors)) {
          $actor = $this->createActor($targetClass);
          $this->actors[$targetClass] = $actor;
        }
        return $this->actors[$targetClass];
    }

    /*
     * =========================================================================
     * Dependency setters.
     * =========================================================================
     */

    /**
     * Set whether or not to cache aspects. If set to false, all returned
     * aspects will be new instances.
     *
     * @param boolean $useCache
     */
    public function setCacheEnabled($useCache)
    {
        $this->useCache = $useCache;
    }

    /**
     * Set the naming strategy to use when retrieving aspects.
     *
     * @param ActorNamingStrategyInterface $naminStrategy
     */
    public function setNamingStrategy(
      ActorNamingStrategyInterface $namingStrategy
    ) {
        $this->namingStrategy = $namingStrategy;
    }

    /*
     * =========================================================================
     * Private helpers.
     * =========================================================================
     */

    /* Instantiate an aspect for the specified class. */
    private function createActor($targetClass)
    {
        $actorName = $this->namingStrategy->getActorName($targetClass);
        $fq = $this->baseNamespace . "\\$actorName";
        return new $fq();
    }
}
