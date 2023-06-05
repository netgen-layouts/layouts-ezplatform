<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Item\ValueLoader;

use Exception;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Ez\Item\ValueLoader\LocationValueLoader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LocationValueLoaderTest extends TestCase
{
    private MockObject $locationServiceMock;

    private LocationValueLoader $valueLoader;

    protected function setUp(): void
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->valueLoader = new LocationValueLoader($this->locationServiceMock);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Item\ValueLoader\LocationValueLoader::__construct
     * @covers \Netgen\Layouts\Ez\Item\ValueLoader\LocationValueLoader::load
     */
    public function testLoad(): void
    {
        $location = new Location(
            [
                'id' => 52,
                'contentInfo' => new ContentInfo(
                    [
                        'published' => true,
                    ],
                ),
            ],
        );

        $this->locationServiceMock
            ->method('loadLocation')
            ->with(self::identicalTo(52))
            ->willReturn($location);

        self::assertSame($location, $this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Item\ValueLoader\LocationValueLoader::load
     */
    public function testLoadWithNoLocation(): void
    {
        $this->locationServiceMock
            ->method('loadLocation')
            ->with(self::identicalTo(52))
            ->willThrowException(new Exception());

        self::assertNull($this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Item\ValueLoader\LocationValueLoader::load
     */
    public function testLoadWithNonPublishedContent(): void
    {
        $this->locationServiceMock
            ->method('loadLocation')
            ->with(self::identicalTo(52))
            ->willReturn(
                new Location(
                    [
                        'contentInfo' => new ContentInfo(
                            [
                                'published' => false,
                            ],
                        ),
                    ],
                ),
            );

        self::assertNull($this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteId(): void
    {
        $location = new Location(
            [
                'remoteId' => 'abc',
                'contentInfo' => new ContentInfo(
                    [
                        'published' => true,
                    ],
                ),
            ],
        );

        $this->locationServiceMock
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn($location);

        self::assertSame($location, $this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdWithNoLocation(): void
    {
        $this->locationServiceMock
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willThrowException(new Exception());

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdWithNonPublishedContent(): void
    {
        $this->locationServiceMock
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn(
                new Location(
                    [
                        'contentInfo' => new ContentInfo(
                            [
                                'published' => false,
                            ],
                        ),
                    ],
                ),
            );

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }
}
