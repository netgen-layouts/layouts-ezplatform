<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\TargetBuilder\Builder;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Location;
use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\Location as LocationTarget;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class LocationTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Location
     */
    protected $targetBuilder;

    /**
     * Sets up the route target builder tests.
     */
    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('locationId', 42);

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->targetBuilder = new Location();
        $this->targetBuilder->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Location::buildTarget
     */
    public function testBuildTarget()
    {
        self::assertEquals(new LocationTarget(array(42)), $this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Location::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertFalse($this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Location::buildTarget
     */
    public function testBuildTargetWithNoLocationId()
    {
        // Make sure we have no location ID attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('locationId');

        self::assertFalse($this->targetBuilder->buildTarget());
    }
}
