<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Item\ValueConverter;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter;
use PHPUnit\Framework\TestCase;

final class ContentValueConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $locationServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $translationHelperMock;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter
     */
    private $valueConverter;

    public function setUp(): void
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
            ->will(
                $this->returnCallback(
                    function ($id): Location {
                        return new Location(['id' => $id, 'invisible' => false]);
                    }
                )
            );

        $this->translationHelperMock = $this->createMock(TranslationHelper::class);

        $this->translationHelperMock
            ->expects($this->any())
            ->method('getTranslatedContentNameByContentInfo')
            ->with($this->isInstanceOf(ContentInfo::class))
            ->will($this->returnValue('Cool name'));

        $this->valueConverter = new ContentValueConverter(
            $this->locationServiceMock,
            $this->translationHelperMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::supports
     */
    public function testSupports(): void
    {
        $this->assertTrue($this->valueConverter->supports(new ContentInfo()));
        $this->assertFalse($this->valueConverter->supports(new Location()));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::getValueType
     */
    public function testGetValueType(): void
    {
        $this->assertSame(
            'ezcontent',
            $this->valueConverter->getValueType(
                new ContentInfo()
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::getId
     */
    public function testGetId(): void
    {
        $this->assertSame(
            24,
            $this->valueConverter->getId(
                new ContentInfo(['id' => 24, 'mainLocationId' => 42])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::getRemoteId
     */
    public function testGetRemoteId(): void
    {
        $this->assertSame(
            'abc',
            $this->valueConverter->getRemoteId(
                new ContentInfo(['remoteId' => 'abc'])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::getName
     */
    public function testGetName(): void
    {
        $this->assertSame(
            'Cool name',
            $this->valueConverter->getName(
                new ContentInfo(['mainLocationId' => 42])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::getIsVisible
     */
    public function testGetIsVisible(): void
    {
        $this->assertTrue(
            $this->valueConverter->getIsVisible(
                new ContentInfo(['mainLocationId' => 42])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::getObject
     */
    public function testGetObject(): void
    {
        $object = new ContentInfo(['id' => 42]);

        $this->assertSame($object, $this->valueConverter->getObject($object));
    }
}
