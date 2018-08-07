<?php

declare(strict_types=1);

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

    public function setUp(): void
    {
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->urlGenerator = new LocationValueUrlGenerator($this->urlGeneratorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlGenerator\LocationValueUrlGenerator::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlGenerator\LocationValueUrlGenerator::generate
     */
    public function testGenerate(): void
    {
        $location = new Location();

        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(self::identicalTo($location))
            ->will(self::returnValue('/location/path'));

        self::assertSame('/location/path', $this->urlGenerator->generate($location));
    }
}
