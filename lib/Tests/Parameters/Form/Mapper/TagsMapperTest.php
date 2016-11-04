<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Parameters\Parameter\Tags as TagsParameter;
use Netgen\BlockManager\Ez\Parameters\Form\Mapper\TagsMapper;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType;
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
            $this->mapper->mapOptions(new TagsParameter(), 'name', array())
        );
    }
}
