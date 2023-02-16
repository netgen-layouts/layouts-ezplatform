<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Tests\DependencyInjection\ConfigurationNode;

final class ComponentNodeTest extends ConfigurationNodeTestBase
{
    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ConfigurationNode\ComponentNode::getConfigurationNode
     */
    public function testComponentSettings(): void
    {
        $config = [
            [
                'system' => [
                    'default' => [
                        'ezcomponent' => [
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
                    'ezcomponent' => [
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
            'system.*.ezcomponent',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ConfigurationNode\ComponentNode::getConfigurationNode
     */
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
                    'ezcomponent' => [
                        'default_parent_location' => 2,
                        'parent_locations' => [],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'system.*.ezcomponent',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ConfigurationNode\ComponentNode::getConfigurationNode
     */
    public function testComponentSettingsWithInvalidDefaultParentLocation(): void
    {
        $config = [
            [
                'system' => [
                    'default' => [
                        'ezcomponent' => [
                            'default_parent_location' => '42',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid(
            $config,
            '/^Invalid type for path "netgen_layouts.system.default.ezcomponent.default_parent_location". Expected "?int(eger)?"?, but got "?string"?\.?$/',
            true,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ConfigurationNode\ComponentNode::getConfigurationNode
     */
    public function testComponentSettingsWithInvalidParentLocations(): void
    {
        $config = [
            [
                'system' => [
                    'default' => [
                        'ezcomponent' => [
                            'parent_locations' => 42,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid(
            $config,
            '/^Invalid type for path "netgen_layouts.system.default.ezcomponent.parent_locations". Expected "?array"?, but got "?int(eger)?"?\.?$/',
            true,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ConfigurationNode\ComponentNode::getConfigurationNode
     */
    public function testComponentSettingsWithNonScalarParentLocation(): void
    {
        $config = [
            [
                'system' => [
                    'default' => [
                        'ezcomponent' => [
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
            '/^Invalid type for path "netgen_layouts.system.default.ezcomponent.parent_locations.foo". Expected "?scalar"?, but got "?array"?\.?$/',
            true,
        );
    }
}
