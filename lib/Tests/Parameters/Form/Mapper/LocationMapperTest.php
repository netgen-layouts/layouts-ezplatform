<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Parameters\Parameter\Location as LocationParameter;
use Netgen\BlockManager\Ez\Parameters\Form\Mapper\LocationMapper;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;
use PHPUnit\Framework\TestCase;

class LocationMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\Form\Mapper\LocationMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new LocationMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\LocationMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentBrowserType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\LocationMapper::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(
                'item_type' => 'ezlocation',
            ),
            $this->mapper->mapOptions(new LocationParameter(), 'name', array())
        );
    }
}
