<?php

namespace Netgen\BlockManager\Ez\Tests\Item\ValueConverter;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter;
use PHPUnit\Framework\TestCase;

final class LocationValueConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $translationHelperMock;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter
     */
    private $valueConverter;

    public function setUp()
    {
        $this->translationHelperMock = $this->createMock(TranslationHelper::class);

        $this->translationHelperMock
            ->expects($this->any())
            ->method('getTranslatedContentNameByContentInfo')
            ->with($this->isInstanceOf(ContentInfo::class))
            ->will($this->returnValue('Cool name'));

        $this->valueConverter = new LocationValueConverter(
            $this->translationHelperMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::supports
     */
    public function testSupports()
    {
        $this->assertTrue($this->valueConverter->supports(new Location()));
        $this->assertFalse($this->valueConverter->supports(new ContentInfo()));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::getValueType
     */
    public function testGetValueType()
    {
        $this->assertEquals(
            'ezlocation',
            $this->valueConverter->getValueType(
                new Location()
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::getId
     */
    public function testGetId()
    {
        $this->assertEquals(
            24,
            $this->valueConverter->getId(
                new Location(['id' => 24])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::getRemoteId
     */
    public function testGetRemoteId()
    {
        $this->assertEquals(
            'abc',
            $this->valueConverter->getRemoteId(
                new Location(['remoteId' => 'abc'])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::getName
     */
    public function testGetName()
    {
        $this->assertEquals(
            'Cool name',
            $this->valueConverter->getName(
                new Location(['contentInfo' => new ContentInfo()])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::getIsVisible
     */
    public function testGetIsVisible()
    {
        $this->assertTrue(
            $this->valueConverter->getIsVisible(
                new Location(['invisible' => false])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\LocationValueConverter::getObject
     */
    public function testGetObject()
    {
        $object = new Location(['id' => 42]);

        $this->assertEquals($object, $this->valueConverter->getObject($object));
    }
}
