<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\TargetBuilder;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\LocationService;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Subtree;
use Netgen\BlockManager\Layout\Resolver\Target;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class SubtreeTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationServiceMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Subtree
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

        $this->locationServiceMock = $this->getMock(LocationService::class);

        $this->targetBuilder = new Subtree($this->locationServiceMock);
        $this->targetBuilder->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Subtree::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Subtree::buildTarget
     */
    public function testBuildTarget()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Location(array('pathString' => '/1/2/42/'))));

        self::assertEquals(
            new Target(array('identifier' => 'subtree', 'values' => array(1, 2, 42))),
            $this->targetBuilder->buildTarget()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Subtree::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Subtree::buildTarget
     */
    public function testBuildTargetWithNoLocationId()
    {
        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        // Make sure we have no location ID attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('locationId');

        self::assertNull($this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Subtree::buildTarget
     */
    public function testBuildTargetWithNoLocation()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('location', 42)));

        self::assertNull($this->targetBuilder->buildTarget());
    }
}
