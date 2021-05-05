<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Item\ValueConverter;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Netgen\Layouts\Ez\Item\ValueConverter\LocationValueConverter;
use PHPUnit\Framework\TestCase;

final class LocationValueConverterTest extends TestCase
{
    private LocationValueConverter $valueConverter;

    protected function setUp(): void
    {
        $this->valueConverter = new LocationValueConverter();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\LocationValueConverter::supports
     */
    public function testSupports(): void
    {
        self::assertTrue($this->valueConverter->supports(new Location()));
        self::assertFalse($this->valueConverter->supports(new ContentInfo()));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\LocationValueConverter::getValueType
     */
    public function testGetValueType(): void
    {
        self::assertSame(
            'ezlocation',
            $this->valueConverter->getValueType(
                new Location(),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\LocationValueConverter::getId
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
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\LocationValueConverter::getRemoteId
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
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\LocationValueConverter::getName
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
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\LocationValueConverter::getIsVisible
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
     * @covers \Netgen\Layouts\Ez\Item\ValueConverter\LocationValueConverter::getObject
     */
    public function testGetObject(): void
    {
        $object = new Location(['id' => 42]);

        self::assertSame($object, $this->valueConverter->getObject($object));
    }
}
