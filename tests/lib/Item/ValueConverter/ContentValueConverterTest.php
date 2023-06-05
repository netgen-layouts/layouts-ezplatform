<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Item\ValueConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Netgen\Layouts\Ez\Item\ValueConverter\ContentValueConverter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ContentValueConverterTest extends TestCase
{
    private MockObject $locationServiceMock;

    private MockObject $contentServiceMock;

    private ContentValueConverter $valueConverter;

    protected function setUp(): void
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->contentServiceMock = $this->createMock(ContentService::class);

        $this->locationServiceMock
            ->method('loadLocation')
            ->with(self::isType('int'))
            ->willReturnCallback(
                static fn ($id): Location => new Location(['id' => $id, 'invisible' => false]),
            );

        $this->contentServiceMock
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
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\ContentValueConverter::__construct
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\ContentValueConverter::supports
     */
    public function testSupports(): void
    {
        self::assertTrue($this->valueConverter->supports(new ContentInfo()));
        self::assertFalse($this->valueConverter->supports(new Location()));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\ContentValueConverter::getValueType
     */
    public function testGetValueType(): void
    {
        self::assertSame(
            'ezcontent',
            $this->valueConverter->getValueType(
                new ContentInfo(),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\ContentValueConverter::getId
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
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\ContentValueConverter::getRemoteId
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
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\ContentValueConverter::getName
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
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\ContentValueConverter::getIsVisible
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
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\ContentValueConverter::getObject
     */
    public function testGetObject(): void
    {
        $object = new ContentInfo(['id' => 42]);

        self::assertSame($object, $this->valueConverter->getObject($object));
    }
}
