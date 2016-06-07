<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Item\ValueLoader;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\EzContentValueLoader;
use Exception;

class EzContentValueLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentServiceMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\EzContentValueLoader
     */
    protected $valueLoader;

    public function setUp()
    {
        $this->contentServiceMock = $this->getMock(ContentService::class);

        $this->valueLoader = new EzContentValueLoader($this->contentServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\EzContentValueLoader::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\EzContentValueLoader::load
     */
    public function testLoad()
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfo')
            ->with($this->isType('int'))
            ->will($this->returnCallback(
                function ($id) { return new ContentInfo(array('id' => $id)); })
            );

        $contentInfo = $this->valueLoader->load(52);

        self::assertEquals(new ContentInfo(array('id' => 52)), $contentInfo);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\EzContentValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\InvalidItemException
     */
    public function testLoadThrowsInvalidItemException()
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfo')
            ->with($this->isType('int'))
            ->will($this->throwException(new Exception()));

        $this->valueLoader->load(52);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\EzContentValueLoader::getValueType
     */
    public function testGetValueType()
    {
        self::assertEquals('ezcontent', $this->valueLoader->getValueType());
    }
}
