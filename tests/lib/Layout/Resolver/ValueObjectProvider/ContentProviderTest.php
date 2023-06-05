<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\ValueObjectProvider;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Netgen\Layouts\Ez\Layout\Resolver\ValueObjectProvider\ContentProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ContentProviderTest extends TestCase
{
    private MockObject $repositoryMock;

    private MockObject $contentServiceMock;

    private ContentProvider $valueObjectProvider;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(Repository::class);
        $this->contentServiceMock = $this->createMock(ContentService::class);

        $this->repositoryMock
            ->method('getContentService')
            ->willReturn($this->contentServiceMock);

        $this->repositoryMock
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->valueObjectProvider = new ContentProvider(
            $this->repositoryMock,
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ValueObjectProvider\ContentProvider::__construct
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ValueObjectProvider\ContentProvider::getValueObject
     */
    public function testGetValueObject(): void
    {
        $content = new Content(
            [
                'versionInfo' => new VersionInfo(
                    [
                        'contentInfo' => new ContentInfo(['mainLocationId' => 24]),
                    ],
                ),
            ],
        );

        $this->contentServiceMock
            ->method('loadContent')
            ->with(self::identicalTo(42))
            ->willReturn($content);

        self::assertSame($content, $this->valueObjectProvider->getValueObject(42));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ValueObjectProvider\ContentProvider::getValueObject
     */
    public function testGetValueObjectWithNonExistentLocation(): void
    {
        $this->contentServiceMock
            ->method('loadContent')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('content', 42));

        self::assertNull($this->valueObjectProvider->getValueObject(42));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ValueObjectProvider\ContentProvider::getValueObject
     */
    public function testGetValueObjectWithNoMainLocation(): void
    {
        $content = new Content(
            [
                'versionInfo' => new VersionInfo(
                    [
                        'contentInfo' => new ContentInfo(),
                    ],
                ),
            ],
        );

        $this->contentServiceMock
            ->method('loadContent')
            ->with(self::identicalTo(42))
            ->willReturn($content);

        self::assertNull($this->valueObjectProvider->getValueObject(42));
    }
}
