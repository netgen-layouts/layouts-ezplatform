<?php

namespace Netgen\BlockManager\Ez\Tests\Item\ValueUrlGenerator;

use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Ez\Item\ValueUrlGenerator\LocationValueUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocationValueUrlGeneratorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $urlGeneratorMock;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueUrlGenerator\LocationValueUrlGenerator
     */
    private $urlGenerator;

    public function setUp()
    {
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->urlGenerator = new LocationValueUrlGenerator($this->urlGeneratorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlGenerator\LocationValueUrlGenerator::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlGenerator\LocationValueUrlGenerator::generate
     */
    public function testGenerate()
    {
        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo(new Location()))
            ->will($this->returnValue('/location/path'));

        $this->assertEquals('/location/path', $this->urlGenerator->generate(new Location()));
    }
}
