<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Item\ValueLoader;

use Exception;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader;
use PHPUnit\Framework\TestCase;

final class ContentValueLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentServiceMock;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader
     */
    private $valueLoader;

    public function setUp(): void
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);

        $this->valueLoader = new ContentValueLoader($this->contentServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::load
     */
    public function testLoad(): void
    {
        $contentInfo = new ContentInfo(
            [
                'id' => 52,
                'published' => true,
                'mainLocationId' => 42,
            ]
        );

        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfo')
            ->with($this->identicalTo(52))
            ->will($this->returnValue($contentInfo));

        $this->assertSame($contentInfo, $this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::load
     */
    public function testLoadWithNoContent(): void
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfo')
            ->with($this->identicalTo(52))
            ->will($this->throwException(new Exception()));

        $this->assertNull($this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::load
     */
    public function testLoadWithNonPublishedContent(): void
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfo')
            ->with($this->identicalTo(52))
            ->will(
                $this->returnValue(
                    new ContentInfo(
                        [
                            'published' => false,
                            'mainLocationId' => 42,
                        ]
                    )
                )
            );

        $this->assertNull($this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::load
     */
    public function testLoadWithNoMainLocation(): void
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfo')
            ->with($this->identicalTo(52))
            ->will(
                $this->returnValue(
                    new ContentInfo(
                        [
                            'published' => true,
                        ]
                    )
                )
            );

        $this->assertNull($this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteId(): void
    {
        $contentInfo = new ContentInfo(
            [
                'remoteId' => 'abc',
                'published' => true,
                'mainLocationId' => 42,
            ]
        );

        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfoByRemoteId')
            ->with($this->identicalTo('abc'))
            ->will($this->returnValue($contentInfo));

        $this->assertSame($contentInfo, $this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdWithNoContent(): void
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfoByRemoteId')
            ->with($this->identicalTo('abc'))
            ->will($this->throwException(new Exception()));

        $this->assertNull($this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdWithNonPublishedContent(): void
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfoByRemoteId')
            ->with($this->identicalTo('abc'))
            ->will(
                $this->returnValue(
                    new ContentInfo(
                        [
                            'published' => false,
                            'mainLocationId' => 42,
                        ]
                    )
                )
            );

        $this->assertNull($this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdWithNoMainLocation(): void
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfoByRemoteId')
            ->with($this->identicalTo('abc'))
            ->will(
                $this->returnValue(
                    new ContentInfo(
                        [
                            'published' => true,
                        ]
                    )
                )
            );

        $this->assertNull($this->valueLoader->loadByRemoteId('abc'));
    }
}
