<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Parameters\Form\Mapper;

use eZ\Publish\API\Repository\Repository;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\Layouts\Ez\Parameters\Form\Mapper\ContentMapper;
use Netgen\Layouts\Ez\Parameters\ParameterType\ContentType as ParameterType;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ValueObjectProviderInterface;
use PHPUnit\Framework\TestCase;

final class ContentMapperTest extends TestCase
{
    private ContentMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ContentMapper();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\Form\Mapper\ContentMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ContentBrowserType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\Form\Mapper\ContentMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $mappedOptions = $this->mapper->mapOptions(
            ParameterDefinition::fromArray(
                [
                    'type' => new ParameterType(
                        $this->createMock(Repository::class),
                        $this->createMock(ValueObjectProviderInterface::class),
                    ),
                    'options' => [
                        'allowed_types' => ['user', 'image'],
                    ],
                ],
            ),
        );

        self::assertSame(
            [
                'item_type' => 'ezcontent',
                'block_prefix' => 'ngcb_ezcontent',
                'custom_params' => [
                    'allowed_content_types' => ['user', 'image'],
                ],
            ],
            $mappedOptions,
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\Form\Mapper\ContentMapper::mapOptions
     */
    public function testMapOptionsEmptyAllowedTypes(): void
    {
        $mappedOptions = $this->mapper->mapOptions(
            ParameterDefinition::fromArray(
                [
                    'type' => new ParameterType(
                        $this->createMock(Repository::class),
                        $this->createMock(ValueObjectProviderInterface::class),
                    ),
                    'options' => [
                        'allowed_types' => [],
                    ],
                ],
            ),
        );

        self::assertSame(
            [
                'item_type' => 'ezcontent',
                'block_prefix' => 'ngcb_ezcontent',
            ],
            $mappedOptions,
        );
    }
}
