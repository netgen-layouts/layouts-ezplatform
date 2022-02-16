<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Item\ValueConverter;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Netgen\Layouts\Ibexa\Item\ValueConverter\LocationValueConverter;
use PHPUnit\Framework\TestCase;

final class LocationValueConverterTest extends TestCase
{
    private LocationValueConverter $valueConverter;

    protected function setUp(): void
    {
        $this->valueConverter = new LocationValueConverter();
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\LocationValueConverter::supports
     */
    public function testSupports(): void
    {
        self::assertTrue($this->valueConverter->supports(new Location()));
        self::assertFalse($this->valueConverter->supports(new ContentInfo()));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\LocationValueConverter::getValueType
     */
    public function testGetValueType(): void
    {
        self::assertSame(
            'ibexa_location',
            $this->valueConverter->getValueType(
                new Location(),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\LocationValueConverter::getId
     */
    public function testGetId(): void
    {
        self::assertSame(
            24,
            $this->valueConverter->getId(
                new Location(['id' => 24]),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\LocationValueConverter::getRemoteId
     */
    public function testGetRemoteId(): void
    {
        self::assertSame(
            'abc',
            $this->valueConverter->getRemoteId(
                new Location(['remoteId' => 'abc']),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\LocationValueConverter::getName
     */
    public function testGetName(): void
    {
        self::assertSame(
            'Cool name',
            $this->valueConverter->getName(
                new Location(
                    [
                        'content' => new Content(
                            [
                                'versionInfo' => new VersionInfo(
                                    [
                                        'prioritizedNameLanguageCode' => 'cro-HR',
                                        'names' => ['cro-HR' => 'Cool name'],
                                    ],
                                ),
                            ],
                        ),
                    ],
                ),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\LocationValueConverter::getIsVisible
     */
    public function testGetIsVisible(): void
    {
        self::assertTrue(
            $this->valueConverter->getIsVisible(
                new Location(['invisible' => false]),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueConverter\LocationValueConverter::getObject
     */
    public function testGetObject(): void
    {
        $object = new Location(['id' => 42]);

        self::assertSame($object, $this->valueConverter->getObject($object));
    }
}
