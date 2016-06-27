<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Item\ValueLoader;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\LocationValueLoader;
use Netgen\BlockManager\Exception\InvalidItemException;
use PHPUnit\Framework\TestCase;

class EzLocationValueLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationServiceMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\LocationValueLoader
     */
    protected $valueLoader;

    public function setUp()
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->valueLoader = new LocationValueLoader($this->locationServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\LocationValueLoader::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\LocationValueLoader::load
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

        self::assertEquals($location, $this->valueLoader->load(52));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\LocationValueLoader::load
     * @expectedException \Netgen\BlockManager\Exception\InvalidItemException
     */
    public function testLoadThrowsInvalidItemException()
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
            ->will($this->throwException(new InvalidItemException()));

        $this->valueLoader->load(52);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader\LocationValueLoader::getValueType
     */
    public function testGetValueType()
    {
        self::assertEquals('ezlocation', $this->valueLoader->getValueType());
    }
}
