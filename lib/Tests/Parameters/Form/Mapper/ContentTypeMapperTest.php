<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Ez\Parameters\Parameter\ContentType as ContentTypeParameter;
use Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentTypeMapper;
use PHPUnit\Framework\TestCase;

class ContentTypeMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentTypeMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new ContentTypeMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentTypeMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentTypeType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentTypeMapper::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(
                'multiple' => true,
            ),
            $this->mapper->mapOptions(
                new ContentTypeParameter(array('multiple' => true)),
                'name',
                array()
            )
        );
    }
}
