<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Item\ValueConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter;
use PHPUnit\Framework\TestCase;

final class LocationValueConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentServiceMock;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter
     */
    private $valueConverter;

    public function setUp(): void
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);

        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadVersionInfo')
            ->with(self::isInstanceOf(ContentInfo::class))
            ->will(
                self::returnValue(
                    new VersionInfo(
                        [
                            'prioritizedNameLanguageCode' => 'eng-GB',
                            'names' => ['eng-GB' => 'Cool name'],
                        ]
                    )
                )
            );

        $this->valueConverter = new LocationValueConverter(
            $this->contentServiceMock,
            $this->createMock(TranslationHelper::class)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::supports
     */
    public function testSupports(): void
    {
        self::assertTrue($this->valueConverter->supports(new Location()));
        self::assertFalse($this->valueConverter->supports(new ContentInfo()));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::getValueType
     */
    public function testGetValueType(): void
    {
        self::assertSame(
            'ezlocation',
            $this->valueConverter->getValueType(
                new Location()
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::getId
     */
    public function testGetId(): void
    {
        self::assertSame(
            24,
            $this->valueConverter->getId(
                new Location(['id' => 24])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::getRemoteId
     */
    public function testGetRemoteId(): void
    {
        self::assertSame(
            'abc',
            $this->valueConverter->getRemoteId(
                new Location(['remoteId' => 'abc'])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::getName
     */
    public function testGetName(): void
    {
        self::assertSame(
            'Cool name',
            $this->valueConverter->getName(
                new Location(['contentInfo' => new ContentInfo()])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::getIsVisible
     */
    public function testGetIsVisible(): void
    {
        self::assertTrue(
            $this->valueConverter->getIsVisible(
                new Location(['invisible' => false])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::getObject
     */
    public function testGetObject(): void
    {
        $object = new Location(['id' => 42]);

        self::assertSame($object, $this->valueConverter->getObject($object));
    }
}
