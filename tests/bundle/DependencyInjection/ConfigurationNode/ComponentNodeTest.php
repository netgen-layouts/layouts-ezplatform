<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Tests\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection\ConfigurationNode\ComponentNode;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ComponentNode::class)]
final class ComponentNodeTest extends ConfigurationNodeTestBase
{
    public function testComponentSettings(): void
    {
        $config = [
            [
                'system' => [
                    'default' => [
                        'ibexa_component' => [
                            'default_parent_location' => 42,
                            'parent_locations' => [
                                'foo' => 24,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'system' => [
                'default' => [
                    'ibexa_component' => [
                        'default_parent_location' => 42,
                        'parent_locations' => [
                            'foo' => 24,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'system.*.ibexa_component',
        );
    }

    public function testDefaultComponentSettings(): void
    {
        $config = [
            [
                'system' => [
                    'default' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'system' => [
                'default' => [
                    'ibexa_component' => [
                        'default_parent_location' => 2,
                        'parent_locations' => [],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'system.*.ibexa_component',
        );
    }

    public function testComponentSettingsWithInvalidDefaultParentLocation(): void
    {
        self::markTestSkipped('Requires update to matthiasnoback/symfony-config-test');

        $config = [
            [
                'system' => [
                    'default' => [
                        'ibexa_component' => [
                            'default_parent_location' => '42',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid(
            $config,
            '/^Invalid type for path "netgen_layouts.system.default.ibexa_component.default_parent_location". Expected "?int(eger)?"?, but got "?string"?\.?$/',
            true,
        );
    }

    public function testComponentSettingsWithInvalidParentLocations(): void
    {
        self::markTestSkipped('Requires update to matthiasnoback/symfony-config-test');

        $config = [
            [
                'system' => [
                    'default' => [
                        'ibexa_component' => [
                            'parent_locations' => 42,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid(
            $config,
            '/^Invalid type for path "netgen_layouts.system.default.ibexa_component.parent_locations". Expected "?array"?, but got "?int(eger)?"?\.?$/',
            true,
        );
    }

    public function testComponentSettingsWithNonScalarParentLocation(): void
    {
        self::markTestSkipped('Requires update to matthiasnoback/symfony-config-test');

        $config = [
            [
                'system' => [
                    'default' => [
                        'ibexa_component' => [
                            'parent_locations' => [
                                'foo' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid(
            $config,
            '/^Invalid type for path "netgen_layouts.system.default.ibexa_component.parent_locations.foo". Expected "?scalar"?, but got "?array"?\.?$/',
            true,
        );
    }
}
