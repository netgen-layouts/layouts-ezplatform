<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Collection\ValueLoader;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\Bundle\EzPublishBlockManagerBundle\Collection\ValueLoader\EzLocationValueLoader;

class EzLocationValueLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationServiceMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Collection\ValueLoader\EzLocationValueLoader
     */
    protected $valueLoader;

    public function setUp()
    {
        $this->locationServiceMock = $this->getMock(LocationService::class);

        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->with($this->isType('int'))
            ->will($this->returnCallback(
                function ($id) { return new Location(array('id' => $id)); })
            );

        $this->valueLoader = new EzLocationValueLoader($this->locationServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Collection\ValueLoader\EzLocationValueLoader::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Collection\ValueLoader\EzLocationValueLoader::load
     */
    public function testLoad()
    {
        $location = $this->valueLoader->load(52);

        self::assertEquals(new Location(array('id' => 52)), $location);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Collection\ValueLoader\EzLocationValueLoader::getValueType
     */
    public function testGetValueType()
    {
        self::assertEquals('ezlocation', $this->valueLoader->getValueType());
    }
}
