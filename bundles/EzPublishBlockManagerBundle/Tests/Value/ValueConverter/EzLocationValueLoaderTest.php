<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Value\ValueConverter;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Helper\TranslationHelper;
use Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzLocationValueConverter;

class EzLocationValueLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translationHelperMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzLocationValueConverter
     */
    protected $valueConverter;

    public function setUp()
    {
        $this->translationHelperMock = $this->getMockBuilder(TranslationHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->translationHelperMock
            ->expects($this->any())
            ->method('getTranslatedContentNameByContentInfo')
            ->with($this->isInstanceOf(ContentInfo::class))
            ->will($this->returnValue('Cool name'));

        $this->valueConverter = new EzLocationValueConverter(
            $this->translationHelperMock
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzLocationValueConverter::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzLocationValueConverter::supports
     */
    public function testSupports()
    {
        self::assertTrue($this->valueConverter->supports(new Location()));
        self::assertFalse($this->valueConverter->supports(new ContentInfo()));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzLocationValueConverter::getValueType
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzLocationValueConverter::getId
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzLocationValueConverter::getName
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzLocationValueConverter::getIsVisible
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
