<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Parameters\Form\Mapper;

use Ibexa\Contracts\Core\Repository\Repository;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\Layouts\Ibexa\Parameters\Form\Mapper\LocationMapper;
use Netgen\Layouts\Ibexa\Parameters\ParameterType\LocationType as ParameterType;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ValueObjectProviderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LocationMapper::class)]
final class LocationMapperTest extends TestCase
{
    private LocationMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new LocationMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(ContentBrowserType::class, $this->mapper->getFormType());
    }

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
                'item_type' => 'ibexa_location',
                'block_prefix' => 'ngcb_ibexa_location',
                'custom_params' => [
                    'allowed_content_types' => ['user', 'image'],
                ],
            ],
            $mappedOptions,
        );
    }

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
                'item_type' => 'ibexa_location',
                'block_prefix' => 'ngcb_ibexa_location',
            ],
            $mappedOptions,
        );
    }
}
