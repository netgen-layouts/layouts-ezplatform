<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Ez\Form\ObjectStateType;
use Netgen\Layouts\Ez\Parameters\Form\Mapper\ObjectStateMapper;
use Netgen\Layouts\Ez\Parameters\ParameterType\ObjectStateType as ParameterType;
use Netgen\Layouts\Parameters\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class ObjectStateMapperTest extends TestCase
{
    private ObjectStateMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ObjectStateMapper();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\Form\Mapper\ObjectStateMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ObjectStateType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\Form\Mapper\ObjectStateMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        self::assertSame(
            [
                'multiple' => true,
                'states' => [42],
            ],
            $this->mapper->mapOptions(
                ParameterDefinition::fromArray(
                    [
                        'type' => new ParameterType(),
                        'options' => [
                            'multiple' => true,
                            'states' => [42],
                        ],
                    ],
                ),
            ),
        );
    }
}
