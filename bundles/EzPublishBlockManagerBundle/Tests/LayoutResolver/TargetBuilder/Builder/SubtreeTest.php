<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\TargetBuilder\Builder;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Subtree;
use Netgen\BlockManager\LayoutResolver\Target;
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
     * Sets up the route target builder tests.
     */
    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('locationId', 42);

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->locationServiceMock = $this->getMock(
            'eZ\Publish\API\Repository\LocationService'
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Subtree::getTargetIdentifier
     */
    public function testGetTargetIdentifier()
    {
        $targetBuilder = new Subtree($this->locationServiceMock);

        self::assertEquals('subtree', $targetBuilder->getTargetIdentifier());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Subtree::buildTarget
     */
    public function testBuildTarget()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Location(array('pathString' => '/1/2/42/'))));

        $targetBuilder = new Subtree($this->locationServiceMock);
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(new Target('subtree', array(1, 2, 42)), $targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Subtree::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        // Make sure we have no request
        $this->requestStack->pop();

        $targetBuilder = new Subtree($this->locationServiceMock);
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(false, $targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Subtree::buildTarget
     */
    public function testBuildTargetWithNoLocationId()
    {
        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        // Make sure we have no location ID attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('locationId');

        $targetBuilder = new Subtree($this->locationServiceMock);
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(false, $targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Subtree::buildTarget
     */
    public function testBuildTargetWithNoLocation()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('location', 42)));

        $targetBuilder = new Subtree($this->locationServiceMock);
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(false, $targetBuilder->buildTarget());
    }
}
