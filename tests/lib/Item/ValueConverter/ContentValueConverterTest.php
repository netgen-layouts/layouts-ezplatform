<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Item\ValueConverter;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Netgen\Layouts\Ibexa\Item\ValueConverter\ContentValueConverter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ContentValueConverterTest extends TestCase
{
    private MockObject&LocationService $locationServiceMock;

    private MockObject&ContentService $contentServiceMock;

    private ContentValueConverter $valueConverter;

    protected function setUp(): void
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->contentServiceMock = $this->createMock(ContentService::class);

        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocation')
            ->with(self::isType('int'))
            ->willReturnCallback(
                static fn ($id): Location => new Location(['id' => $id, 'invisible' => false]),
            );

        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadContentByContentInfo')
            ->with(self::isInstanceOf(ContentInfo::class))
            ->willReturn(
                new Content(
                    [
                        'versionInfo' => new VersionInfo(
                            [
                                'prioritizedNameLanguageCode' => 'eng-GB',
                                'names' => ['eng-GB' => 'Cool name'],
                            ],
                        ),
                    ],
                ),
            );

        $this->valueConverter = new ContentValueConverter(
            $this->locationServiceMock,
            $this->contentServiceMock,
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\ContentValueConverter::__construct
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\ContentValueConverter::supports
     */
    public function testSupports(): void
    {
        self::assertTrue($this->valueConverter->supports(new ContentInfo()));
        self::assertFalse($this->valueConverter->supports(new Location()));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\ContentValueConverter::getValueType
     */
    public function testGetValueType(): void
    {
        self::assertSame(
            'ibexa_content',
            $this->valueConverter->getValueType(
                new ContentInfo(),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\ContentValueConverter::getId
     */
    public function testGetId(): void
    {
        self::assertSame(
            24,
            $this->valueConverter->getId(
                new ContentInfo(['id' => 24, 'mainLocationId' => 42]),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\ContentValueConverter::getRemoteId
     */
    public function testGetRemoteId(): void
    {
        self::assertSame(
            'abc',
            $this->valueConverter->getRemoteId(
                new ContentInfo(['remoteId' => 'abc']),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\ContentValueConverter::getName
     */
    public function testGetName(): void
    {
        self::assertSame(
            'Cool name',
            $this->valueConverter->getName(
                new ContentInfo(['mainLocationId' => 42]),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\ContentValueConverter::getIsVisible
     */
    public function testGetIsVisible(): void
    {
        self::assertTrue(
            $this->valueConverter->getIsVisible(
                new ContentInfo(['mainLocationId' => 42]),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\ContentValueConverter::getObject
     */
    public function testGetObject(): void
    {
        $object = new ContentInfo(['id' => 42]);

        self::assertSame($object, $this->valueConverter->getObject($object));
    }
}
