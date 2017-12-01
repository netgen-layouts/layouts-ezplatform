<?php

namespace Netgen\BlockManager\Ez\Tests\Item\ValueUrlBuilder;

use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Ez\Item\ValueUrlBuilder\LocationValueUrlBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LocationValueUrlBuilderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $urlGenerator;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\LocationValueUrlBuilder
     */
    private $urlBuilder;

    public function setUp()
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $this->urlBuilder = new LocationValueUrlBuilder($this->urlGenerator);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\LocationValueUrlBuilder::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\LocationValueUrlBuilder::getUrl
     */
    public function testGetUrl()
    {
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo(new Location()))
            ->will($this->returnValue('/location/path'));

        $this->assertEquals('/location/path', $this->urlBuilder->getUrl(new Location()));
    }
}
