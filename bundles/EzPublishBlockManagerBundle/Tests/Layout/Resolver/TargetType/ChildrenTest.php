<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\TargetType;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\LocationService;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Children;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class ChildrenTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Children
     */
    protected $targetType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationServiceMock;

    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('locationId', 42);

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->targetType = new Children($this->locationServiceMock);
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Children::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('children', $this->targetType->getIdentifier());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Children::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Children::provideValue
     */
    public function testProvideValue()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Location(array('parentLocationId' => 84))));

        self::assertEquals(
            84,
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Children::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetType->provideValue());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Children::provideValue
     */
    public function testProvideValueWithNoLocationId()
    {
        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        // Make sure we have no location ID attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('locationId');

        self::assertNull($this->targetType->provideValue());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Children::provideValue
     */
    public function testProvideValueWithNoLocation()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('location', 42)));

        self::assertNull($this->targetType->provideValue());
    }
}
