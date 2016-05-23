<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Value\ValueConverter;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Helper\TranslationHelper;
use Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzContentValueConverter;

class EzContentValueLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translationHelperMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzContentValueConverter
     */
    protected $valueConverter;

    public function setUp()
    {
        $this->locationServiceMock = $this->getMock(LocationService::class);

        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
            ->will($this->returnCallback(
                function ($id) { return new Location(array('id' => $id, 'invisible' => false)); })
            );

        $this->translationHelperMock = $this->getMockBuilder(TranslationHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->translationHelperMock
            ->expects($this->any())
            ->method('getTranslatedContentNameByContentInfo')
            ->with($this->isInstanceOf(ContentInfo::class))
            ->will($this->returnValue('Cool name'));

        $this->valueConverter = new EzContentValueConverter(
            $this->locationServiceMock,
            $this->translationHelperMock
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzContentValueConverter::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzContentValueConverter::supports
     */
    public function testSupports()
    {
        self::assertTrue($this->valueConverter->supports(new ContentInfo()));
        self::assertFalse($this->valueConverter->supports(new Location()));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzContentValueConverter::getValueType
     */
    public function testGetValueType()
    {
        self::assertEquals(
            'ezcontent',
            $this->valueConverter->getValueType(
                new ContentInfo()
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzContentValueConverter::getId
     */
    public function testGetId()
    {
        self::assertEquals(
            24,
            $this->valueConverter->getId(
                new ContentInfo(array('id' => 24, 'mainLocationId' => 42))
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzContentValueConverter::getName
     */
    public function testGetName()
    {
        self::assertEquals(
            'Cool name',
            $this->valueConverter->getName(
                new ContentInfo(array('mainLocationId' => 42))
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter\EzContentValueConverter::getIsVisible
     */
    public function testGetIsVisible()
    {
        self::assertTrue(
            $this->valueConverter->getIsVisible(
                new ContentInfo(array('mainLocationId' => 42))
            )
        );
    }
}
