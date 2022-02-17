<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Item\ValueLoader;

use Exception;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Netgen\Layouts\Ibexa\Item\ValueLoader\ContentValueLoader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ContentValueLoaderTest extends TestCase
{
    private MockObject $contentServiceMock;

    private ContentValueLoader $valueLoader;

    protected function setUp(): void
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);

        $this->valueLoader = new ContentValueLoader($this->contentServiceMock);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueLoader\ContentValueLoader::__construct
     * @covers \Netgen\Layouts\Ibexa\Item\ValueLoader\ContentValueLoader::load
     */
    public function testLoad(): void
    {
        $contentInfo = new ContentInfo(
            [
                'id' => 52,
                'published' => true,
                'mainLocationId' => 42,
            ],
        );

        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadContentInfo')
            ->with(self::identicalTo(52))
            ->willReturn($contentInfo);

        self::assertSame($contentInfo, $this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueLoader\ContentValueLoader::load
     */
    public function testLoadWithNoContent(): void
    {
        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadContentInfo')
            ->with(self::identicalTo(52))
            ->willThrowException(new Exception());

        self::assertNull($this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueLoader\ContentValueLoader::load
     */
    public function testLoadWithNonPublishedContent(): void
    {
        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadContentInfo')
            ->with(self::identicalTo(52))
            ->willReturn(
                new ContentInfo(
                    [
                        'published' => false,
                        'mainLocationId' => 42,
                    ],
                ),
            );

        self::assertNull($this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueLoader\ContentValueLoader::load
     */
    public function testLoadWithNoMainLocation(): void
    {
        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadContentInfo')
            ->with(self::identicalTo(52))
            ->willReturn(
                new ContentInfo(
                    [
                        'published' => true,
                    ],
                ),
            );

        self::assertNull($this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueLoader\ContentValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteId(): void
    {
        $contentInfo = new ContentInfo(
            [
                'remoteId' => 'abc',
                'published' => true,
                'mainLocationId' => 42,
            ],
        );

        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadContentInfoByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn($contentInfo);

        self::assertSame($contentInfo, $this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueLoader\ContentValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdWithNoContent(): void
    {
        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadContentInfoByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willThrowException(new Exception());

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueLoader\ContentValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdWithNonPublishedContent(): void
    {
        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadContentInfoByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn(
                new ContentInfo(
                    [
                        'published' => false,
                        'mainLocationId' => 42,
                    ],
                ),
            );

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueLoader\ContentValueLoader::loadByRemoteId
     */
    public function testLoadByRemoteIdWithNoMainLocation(): void
    {
        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadContentInfoByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn(
                new ContentInfo(
                    [
                        'published' => true,
                    ],
                ),
            );

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }
}
