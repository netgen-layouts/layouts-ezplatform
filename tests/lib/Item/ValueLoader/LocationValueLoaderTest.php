<?php

namespace Netgen\BlockManager\Ez\Tests\Item\ValueLoader;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader;
use PHPUnit\Framework\TestCase;

class LocationValueLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationServiceMock;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader
     */
    protected $valueLoader;

    public function setUp()
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->valueLoader = new LocationValueLoader($this->locationServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     */
    public function testLoad()
    {
        $location = new Location(
            array(
                'id' => 52,
                'contentInfo' => new ContentInfo(
                    array(
                        'published' => true,
                    )
                ),
            )
        );

        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
            ->will($this->returnValue($location));

        $this->assertEquals($location, $this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Location with ID "52" could not be loaded.
     */
    public function testLoadThrowsItemException()
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
            ->will($this->throwException(new ItemException()));

        $this->valueLoader->load(52);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueLoader\LocationValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Location with ID "52" has unpublished content and cannot be loaded.
     */
    public function testLoadThrowsItemExceptionWithNonPublishedContent()
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
            ->will(
                $this->returnValue(
                    new Location(
                        array(
                            'contentInfo' => new ContentInfo(
                                array(
                                    'published' => false,
                                )
                            ),
                        )
                    )
                )
            );

        $this->valueLoader->load(52);
    }
}
