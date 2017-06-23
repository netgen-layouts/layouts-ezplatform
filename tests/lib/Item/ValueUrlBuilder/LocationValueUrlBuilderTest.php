<?php

namespace Netgen\BlockManager\Ez\Tests\Item\ValueUrlBuilder;

use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Ez\Item\ValueUrlBuilder\LocationValueUrlBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

class LocationValueUrlBuilderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $router;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\LocationValueUrlBuilder
     */
    protected $urlBuilder;

    public function setUp()
    {
        $this->router = $this->createMock(RouterInterface::class);

        $this->urlBuilder = new LocationValueUrlBuilder($this->router);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\LocationValueUrlBuilder::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\LocationValueUrlBuilder::getUrl
     */
    public function testGetUrl()
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo(new Location()))
            ->will($this->returnValue('/location/path'));

        $this->assertEquals('/location/path', $this->urlBuilder->getUrl(new Location()));
    }
}
