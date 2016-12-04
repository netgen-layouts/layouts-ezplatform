<?php

namespace Netgen\BlockManager\Ez\Tests\Item\ValueLoader;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\BlockManager\Exception\InvalidItemException;
use Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader;
use PHPUnit\Framework\TestCase;

class ContentValueLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentServiceMock;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader
     */
    protected $valueLoader;

    public function setUp()
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);

        $this->valueLoader = new ContentValueLoader($this->contentServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::load
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

        $this->assertEquals($contentInfo, $this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::load
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
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\InvalidItemException
     */
    public function testLoadThrowsInvalidItemExceptionWithNonPublishedContent()
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfo')
            ->with($this->isType('int'))
            ->will(
                $this->returnValue(
                    new ContentInfo(
                        array(
                            'published' => false,
                            'mainLocationId' => 42,
                        )
                    )
                )
            );

        $this->valueLoader->load(52);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\InvalidItemException
     */
    public function testLoadThrowsInvalidItemExceptionWithNoMainLocation()
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfo')
            ->with($this->isType('int'))
            ->will(
                $this->returnValue(
                    new ContentInfo(
                        array(
                            'published' => true,
                        )
                    )
                )
            );

        $this->valueLoader->load(52);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::getValueType
     */
    public function testGetValueType()
    {
        $this->assertEquals('ezcontent', $this->valueLoader->getValueType());
    }
}
