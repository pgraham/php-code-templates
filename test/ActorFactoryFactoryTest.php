<?php
/**
 * =============================================================================
 * Copyright (c) 2013, Philip Graham
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
namespace zpt\pct\test;

use PHPUnit_Framework_TestCase as TestCase;
use zpt\pct\ActorFactoryFactory;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests the functionality of the ActorFactoryFactory class.
 * 
 * @author Philip Graham <philip@zeptech.ca>
 */
class ActorFactoryFactoryTest extends TestCase
{

    private $actorFactoryFactory;

    protected function setUp()
    {
        $this->actorFactoryFactory = new ActorFactoryFactory();

        $namingStrategy = \Mockery::mock('zpt\pct\ActorNamingStrategyInterface');
        $namingStrategy
            ->shouldReceive('getActorName')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function ($name) {
                return $name;
            });

        $this->actorFactoryFactory->setNamingStrategy($namingStrategy);
    }

    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testActorInjector()
    {
        $injector = \Mockery::mock('zpt\pct\ActorInjector');
        $injector->shouldReceive('inject')->once();

        $this->actorFactoryFactory->registerInjector('zpt\dyn', $injector);

        eval("namespace zpt\dyn;class SampleClass {}");

        $factory = $this->actorFactoryFactory->getFactory('zpt\dyn');
        $factory->get('SampleClass');
    }
}
