<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Form\ObjectStateType;
use Netgen\BlockManager\Ez\Parameters\Form\Mapper\ObjectStateMapper;
use Netgen\BlockManager\Ez\Parameters\ParameterType\ObjectStateType as ParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class ObjectStateMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ObjectStateMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new ObjectStateMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ObjectStateMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ObjectStateType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ObjectStateMapper::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(
                'multiple' => true,
                'states' => array(42),
            ),
            $this->mapper->mapOptions(
                new ParameterDefinition(
                    array(
                        'type' => new ParameterType(),
                        'options' => array(
                            'multiple' => true,
                            'states' => array(42),
                        ),
                    )
                )
            )
        );
    }
}
