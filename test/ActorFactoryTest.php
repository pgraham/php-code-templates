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

use \zpt\pct\ActorFactory;
use PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests the functionality of the ActorFactory class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ActorFactoryTest extends TestCase
{

    private $actorFactory;

    protected function setUp()
    {
        $this->actorFactory = new ActorFactory('zpt\dyn');

        $namingStrategy = \Mockery::mock('zpt\pct\ActorNamingStrategyInterface');
        $namingStrategy
            ->shouldReceive('getActorName')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function ($name) {
                return $name;
            });

        $this->actorFactory->setNamingStrategy($namingStrategy);
    }

    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testActorInjector()
    {
        $injector = \Mockery::mock('zpt\pct\ActorInjector');
        $injector->shouldReceive('inject')->once();

        $this->actorFactory->registerInjector($injector);

        eval("namespace zpt\dyn;class SampleClass {}");
        $this->actorFactory->get('SampleClass');
    }
}
