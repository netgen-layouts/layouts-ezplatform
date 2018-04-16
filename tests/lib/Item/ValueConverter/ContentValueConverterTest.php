<?php

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

    public function setUp()
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
            ->will(
                $this->returnCallback(
                    function ($id) {
                        return new Location(array('id' => $id, 'invisible' => false));
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
    public function testSupports()
    {
        $this->assertTrue($this->valueConverter->supports(new ContentInfo()));
        $this->assertFalse($this->valueConverter->supports(new Location()));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::getValueType
     */
    public function testGetValueType()
    {
        $this->assertEquals(
            'ezcontent',
            $this->valueConverter->getValueType(
                new ContentInfo()
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::getId
     */
    public function testGetId()
    {
        $this->assertEquals(
            24,
            $this->valueConverter->getId(
                new ContentInfo(array('id' => 24, 'mainLocationId' => 42))
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::getRemoteId
     */
    public function testGetRemoteId()
    {
        $this->assertEquals(
            'abc',
            $this->valueConverter->getRemoteId(
                new ContentInfo(array('remoteId' => 'abc'))
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::getName
     */
    public function testGetName()
    {
        $this->assertEquals(
            'Cool name',
            $this->valueConverter->getName(
                new ContentInfo(array('mainLocationId' => 42))
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::getIsVisible
     */
    public function testGetIsVisible()
    {
        $this->assertTrue(
            $this->valueConverter->getIsVisible(
                new ContentInfo(array('mainLocationId' => 42))
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueConverter\ContentValueConverter::getObject
     */
    public function testGetObject()
    {
        $object = new ContentInfo(array('id' => 42));

        $this->assertEquals($object, $this->valueConverter->getObject($object));
    }
}
