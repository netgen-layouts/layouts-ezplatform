<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Item\ValueLoader;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Exception\Item\ItemException;
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
            ->with($this->isType('int'))
            ->will($this->returnValue($location));

        $this->assertEquals($location, $this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Location with ID "52" could not be loaded.
     */
    public function testLoadThrowsItemException(): void
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
            ->will($this->throwException(new ItemException()));

        $this->valueLoader->load(52);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Location with ID "52" has unpublished content and cannot be loaded.
     */
    public function testLoadThrowsItemExceptionWithNonPublishedContent(): void
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
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

        $this->valueLoader->load(52);
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
            ->with($this->isType('string'))
            ->will($this->returnValue($location));

        $this->assertEquals($location, $this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Location with remote ID "abc" could not be loaded.
     */
    public function testLoadByRemoteIdThrowsItemException(): void
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocationByRemoteId')
            ->with($this->isType('string'))
            ->will($this->throwException(new ItemException()));

        $this->valueLoader->loadByRemoteId('abc');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::loadByRemoteId
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Location with remote ID "abc" has unpublished content and cannot be loaded.
     */
    public function testLoadByRemoteIdThrowsItemExceptionWithNonPublishedContent(): void
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocationByRemoteId')
            ->with($this->isType('string'))
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

        $this->valueLoader->loadByRemoteId('abc');
    }
}
