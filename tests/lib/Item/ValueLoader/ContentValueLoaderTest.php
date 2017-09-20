<?php

namespace Netgen\BlockManager\Ez\Tests\Item\ValueLoader;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader;
use PHPUnit\Framework\TestCase;

class ContentValueLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contentServiceMock;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader
     */
    private $valueLoader;

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
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Content with ID "52" could not be loaded.
     */
    public function testLoadThrowsItemException()
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfo')
            ->with($this->isType('int'))
            ->will($this->throwException(new ItemException()));

        $this->valueLoader->load(52);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\ContentValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Content with ID "52" is not published and cannot loaded.
     */
    public function testLoadThrowsItemExceptionWithNonPublishedContent()
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
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Content with ID "52" does not have a main location and cannot loaded.
     */
    public function testLoadThrowsItemExceptionWithNoMainLocation()
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
}
