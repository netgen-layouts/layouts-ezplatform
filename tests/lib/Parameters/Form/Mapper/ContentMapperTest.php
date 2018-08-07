<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use eZ\Publish\API\Repository\Repository;
use Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentMapper;
use Netgen\BlockManager\Ez\Parameters\ParameterType\ContentType as ParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use PHPUnit\Framework\TestCase;

final class ContentMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new ContentMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ContentBrowserType::class, $this->mapper->getFormType());
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

        self::assertSame(
            [
                'item_type' => 'ezcontent',
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

        self::assertSame(
            [
                'item_type' => 'ezcontent',
            ],
            $mappedOptions
        );
    }
}
