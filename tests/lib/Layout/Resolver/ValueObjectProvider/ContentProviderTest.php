<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Layout\Resolver\ValueObjectProvider;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Netgen\Layouts\Ibexa\Layout\Resolver\ValueObjectProvider\ContentProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ContentProviderTest extends TestCase
{
    private MockObject&Repository $repositoryMock;

    private MockObject&ContentService $contentServiceMock;

    private ContentProvider $valueObjectProvider;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(Repository::class);
        $this->contentServiceMock = $this->createMock(ContentService::class);

        $this->repositoryMock
            ->expects(self::any())
            ->method('getContentService')
            ->willReturn($this->contentServiceMock);

        $this->repositoryMock
            ->expects(self::any())
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
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ValueObjectProvider\ContentProvider::__construct
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ValueObjectProvider\ContentProvider::getValueObject
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
            ->expects(self::any())
            ->method('loadContent')
            ->with(self::identicalTo(42))
            ->willReturn($content);

        self::assertSame($content, $this->valueObjectProvider->getValueObject(42));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ValueObjectProvider\ContentProvider::getValueObject
     */
    public function testGetValueObjectWithNonExistentLocation(): void
    {
        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadContent')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('content', 42));

        self::assertNull($this->valueObjectProvider->getValueObject(42));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ValueObjectProvider\ContentProvider::getValueObject
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
            ->expects(self::any())
            ->method('loadContent')
            ->with(self::identicalTo(42))
            ->willReturn($content);

        self::assertNull($this->valueObjectProvider->getValueObject(42));
    }
}
