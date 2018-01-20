<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentTypeMapper;
use Netgen\BlockManager\Ez\Parameters\ParameterType\ContentTypeType as ParameterType;
use Netgen\BlockManager\Parameters\Parameter;
use PHPUnit\Framework\TestCase;

final class ContentTypeMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentTypeMapper
     */
    private $mapper;

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
                'types' => array(42),
            ),
            $this->mapper->mapOptions(
                new Parameter(
                    array(
                        'type' => new ParameterType(),
                        'options' => array(
                            'multiple' => true,
                            'types' => array(42),
                        ),
                    )
                )
            )
        );
    }
}
