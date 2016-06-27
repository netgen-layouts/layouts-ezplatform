<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Item\ValueConverter;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Helper\TranslationHelper;
use Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueConverter\LocationValueConverter;
use PHPUnit\Framework\TestCase;

class LocationValueConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translationHelperMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueConverter\LocationValueConverter
     */
    protected $valueConverter;

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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueConverter\LocationValueConverter::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueConverter\LocationValueConverter::supports
     */
    public function testSupports()
    {
        self::assertTrue($this->valueConverter->supports(new Location()));
        self::assertFalse($this->valueConverter->supports(new ContentInfo()));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueConverter\LocationValueConverter::getValueType
     */
    public function testGetValueType()
    {
        self::assertEquals(
            'ezlocation',
            $this->valueConverter->getValueType(
                new Location()
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueConverter\LocationValueConverter::getId
     */
    public function testGetId()
    {
        self::assertEquals(
            24,
            $this->valueConverter->getId(
                new Location(array('id' => 24))
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueConverter\LocationValueConverter::getName
     */
    public function testGetName()
    {
        self::assertEquals(
            'Cool name',
            $this->valueConverter->getName(
                new Location(array('contentInfo' => new ContentInfo()))
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueConverter\LocationValueConverter::getIsVisible
     */
    public function testGetIsVisible()
    {
        self::assertTrue(
            $this->valueConverter->getIsVisible(
                new Location(array('invisible' => false))
            )
        );
    }
}
