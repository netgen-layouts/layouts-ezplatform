<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentTypeMapper;
use Netgen\BlockManager\Ez\Parameters\ParameterType\ContentTypeType as ParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class ContentTypeMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentTypeMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new ContentTypeMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentTypeMapper::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertSame(ContentTypeType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentTypeMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $this->assertSame(
            [
                'multiple' => true,
                'types' => [42],
            ],
            $this->mapper->mapOptions(
                new ParameterDefinition(
                    [
                        'type' => new ParameterType(),
                        'options' => [
                            'multiple' => true,
                            'types' => [42],
                        ],
                    ]
                )
            )
        );
    }
}
