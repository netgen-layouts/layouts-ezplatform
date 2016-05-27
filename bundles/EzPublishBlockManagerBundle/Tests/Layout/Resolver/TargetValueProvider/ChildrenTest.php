<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\TargetValueProvider;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\LocationService;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\Children;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class ChildrenTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\Children
     */
    protected $targetValueProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationServiceMock;

    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('locationId', 42);

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->locationServiceMock = $this->getMock(LocationService::class);

        $this->targetValueProvider = new Children($this->locationServiceMock);
        $this->targetValueProvider->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\Children::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\Children::provideValue
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
            $this->targetValueProvider->provideValue()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\Children::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetValueProvider->provideValue());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\Children::provideValue
     */
    public function testProvideValueWithNoLocationId()
    {
        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        // Make sure we have no location ID attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('locationId');

        self::assertNull($this->targetValueProvider->provideValue());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\Children::provideValue
     */
    public function testProvideValueWithNoLocation()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('location', 42)));

        self::assertNull($this->targetValueProvider->provideValue());
    }
}
