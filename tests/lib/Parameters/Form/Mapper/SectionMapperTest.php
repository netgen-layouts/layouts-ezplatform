<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Form\SectionType;
use Netgen\BlockManager\Ez\Parameters\Form\Mapper\SectionMapper;
use Netgen\BlockManager\Ez\Parameters\ParameterType\SectionType as ParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class SectionMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\Form\Mapper\SectionMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new SectionMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\SectionMapper::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertSame(SectionType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\SectionMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $this->assertSame(
            [
                'multiple' => true,
                'sections' => ['media'],
            ],
            $this->mapper->mapOptions(
                ParameterDefinition::fromArray(
                    [
                        'type' => new ParameterType(),
                        'options' => [
                            'multiple' => true,
                            'sections' => ['media'],
                        ],
                    ]
                )
            )
        );
    }
}
