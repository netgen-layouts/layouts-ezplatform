<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Item\ValueLoader;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\ContentValueLoader;
use Netgen\BlockManager\Exception\InvalidItemException;
use PHPUnit\Framework\TestCase;

class ContentValueLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentServiceMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\ContentValueLoader
     */
    protected $valueLoader;

    public function setUp()
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);

        $this->valueLoader = new ContentValueLoader($this->contentServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\ContentValueLoader::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\ContentValueLoader::load
     */
    public function testLoad()
    {
        $contentInfo = new ContentInfo(
            array(
                'id' => 52,
                'published' => true,
                'mainLocationId' => 42,
            )
        );

        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfo')
            ->with($this->isType('int'))
            ->will($this->returnValue($contentInfo));

        self::assertEquals($contentInfo, $this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\ContentValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\InvalidItemException
     */
    public function testLoadThrowsInvalidItemException()
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfo')
            ->with($this->isType('int'))
            ->will($this->throwException(new InvalidItemException()));

        $this->valueLoader->load(52);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\ContentValueLoader::getValueType
     */
    public function testGetValueType()
    {
        self::assertEquals('ezcontent', $this->valueLoader->getValueType());
    }
}
