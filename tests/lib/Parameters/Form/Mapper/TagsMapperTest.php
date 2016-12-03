<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType as ParameterType;
use Netgen\BlockManager\Ez\Parameters\Form\Mapper\TagsMapper;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType;
use PHPUnit\Framework\TestCase;

class TagsMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\Form\Mapper\TagsMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new TagsMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\TagsMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentBrowserMultipleType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\TagsMapper::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(
                'item_type' => 'eztags',
            ),
            $this->mapper->mapOptions(new Parameter(array('type' => new ParameterType())))
        );
    }
}
