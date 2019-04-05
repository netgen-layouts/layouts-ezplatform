<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Item\ValueLoader;

use Exception;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader;
use PHPUnit\Framework\TestCase;

final class LocationValueLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $locationServiceMock;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader
     */
    private $valueLoader;

    public function setUp(): void
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->valueLoader = new LocationValueLoader($this->locationServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     */
    public function testLoad(): void
    {
        $location = new Location(
            [
                'id' => 52,
                'contentInfo' => new ContentInfo(
                    [
                        'published' => true,
                    ]
                ),
            ]
        );

        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocation')
            ->with(self::identicalTo(52))
            ->willReturn($location);

        self::assertSame($location, $this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     */
    public function testLoadWithNoLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocation')
            ->with(self::identicalTo(52))
            ->willThrowException(new Exception());

        self::assertNull($this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     */
    public function testLoadWithNonPublishedContent(): void
    {
        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocation')
            ->with(self::identicalTo(52))
            ->willReturn(
                new Location(
                    [
                        'contentInfo' => new ContentInfo(
                            [
                                'published' => false,
                            ]
                        ),
                    ]
                )
            );

        self::assertNull($this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteId(): void
    {
        $location = new Location(
            [
                'remoteId' => 'abc',
                'contentInfo' => new ContentInfo(
                    [
                        'published' => true,
                    ]
                ),
            ]
        );

        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn($location);

        self::assertSame($location, $this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdWithNoLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willThrowException(new Exception());

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdWithNonPublishedContent(): void
    {
        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn(
                new Location(
                    [
                        'contentInfo' => new ContentInfo(
                            [
                                'published' => false,
                            ]
                        ),
                    ]
                )
            );

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }
}
