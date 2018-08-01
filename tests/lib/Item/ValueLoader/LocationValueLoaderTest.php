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
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->identicalTo(52))
            ->will($this->returnValue($location));

        $this->assertSame($location, $this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     */
    public function testLoadWithNoLocation(): void
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->identicalTo(52))
            ->will($this->throwException(new Exception()));

        $this->assertNull($this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     */
    public function testLoadWithNonPublishedContent(): void
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->identicalTo(52))
            ->will(
                $this->returnValue(
                    new Location(
                        [
                            'contentInfo' => new ContentInfo(
                                [
                                    'published' => false,
                                ]
                            ),
                        ]
                    )
                )
            );

        $this->assertNull($this->valueLoader->load(52));
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
            ->expects($this->any())
            ->method('loadLocationByRemoteId')
            ->with($this->identicalTo('abc'))
            ->will($this->returnValue($location));

        $this->assertSame($location, $this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdWithNoLocation(): void
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocationByRemoteId')
            ->with($this->identicalTo('abc'))
            ->will($this->throwException(new Exception()));

        $this->assertNull($this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdWithNonPublishedContent(): void
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocationByRemoteId')
            ->with($this->identicalTo('abc'))
            ->will(
                $this->returnValue(
                    new Location(
                        [
                            'contentInfo' => new ContentInfo(
                                [
                                    'published' => false,
                                ]
                            ),
                        ]
                    )
                )
            );

        $this->assertNull($this->valueLoader->loadByRemoteId('abc'));
    }
}
