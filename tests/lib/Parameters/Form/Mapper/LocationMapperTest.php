<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use eZ\Publish\API\Repository\Repository;
use Netgen\BlockManager\Ez\Parameters\Form\Mapper\LocationMapper;
use Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType as ParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use PHPUnit\Framework\TestCase;

final class LocationMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\Form\Mapper\LocationMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new LocationMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\LocationMapper::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertSame(ContentBrowserType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $mappedOptions = $this->mapper->mapOptions(
            ParameterDefinition::fromArray(
                [
                    'type' => new ParameterType($this->createMock(Repository::class)),
                    'options' => [
                        'allowed_types' => ['user', 'image'],
                    ],
                ]
            )
        );

        $this->assertSame(
            [
                'item_type' => 'ezlocation',
                'custom_params' => [
                    'allowed_content_types' => ['user', 'image'],
                ],
            ],
            $mappedOptions
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentMapper::mapOptions
     */
    public function testMapOptionsEmptyAllowedTypes(): void
    {
        $mappedOptions = $this->mapper->mapOptions(
            ParameterDefinition::fromArray(
                [
                    'type' => new ParameterType($this->createMock(Repository::class)),
                    'options' => [
                        'allowed_types' => [],
                    ],
                ]
            )
        );

        $this->assertSame(
            [
                'item_type' => 'ezlocation',
            ],
            $mappedOptions
        );
    }
}
