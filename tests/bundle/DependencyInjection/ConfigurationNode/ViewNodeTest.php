<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

final class ViewNodeTest extends ConfigurationNodeTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testViewSettings()
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [
                                'template' => 'block.html.twig',
                                'match' => [
                                    'block_identifier' => 42,
                                ],
                                'parameters' => [
                                    'param' => 'value',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'template' => 'block.html.twig',
                            'match' => [
                                'block_identifier' => 42,
                            ],
                            'parameters' => [
                                'param' => 'value',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testViewSettingsWithSystemNodeAndDefaultScope()
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [
                                'template' => 'block.html.twig',
                                'match' => [
                                    'block_identifier' => 42,
                                ],
                                'parameters' => [
                                    'param' => 'value',
                                ],
                            ],
                        ],
                    ],
                ],
                'system' => [
                    'default' => [
                        'view' => [
                            'block_view' => [
                                'other_context' => [
                                    'block' => [
                                        'template' => 'block.html.twig',
                                        'match' => [
                                            'block_identifier' => 42,
                                        ],
                                        'parameters' => [
                                            'param2' => 'value2',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'template' => 'block.html.twig',
                            'match' => [
                                'block_identifier' => 42,
                            ],
                            'parameters' => [
                                'param' => 'value',
                            ],
                        ],
                    ],
                    'other_context' => [
                        'block' => [
                            'template' => 'block.html.twig',
                            'match' => [
                                'block_identifier' => 42,
                            ],
                            'parameters' => [
                                'param2' => 'value2',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        // other_context context should not appear in original config, but only in siteaccess aware one
        unset($expectedConfig['view']['block_view']['other_context']);

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testViewSettingsWithSystemNodeAndNonDefaultScope()
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [
                                'template' => 'block.html.twig',
                                'match' => [
                                    'block_identifier' => 42,
                                ],
                                'parameters' => [
                                    'param' => 'value',
                                ],
                            ],
                        ],
                    ],
                ],
                'system' => [
                    'cro' => [
                        'view' => [
                            'block_view' => [
                                'other_context' => [
                                    'block' => [
                                        'template' => 'block.html.twig',
                                        'match' => [
                                            'block_identifier' => 42,
                                        ],
                                        'parameters' => [
                                            'param2' => 'value2',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'template' => 'block.html.twig',
                            'match' => [
                                'block_identifier' => 42,
                            ],
                            'parameters' => [
                                'param' => 'value',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // All configs should have "some_context"
        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);

        // But only "cro" siteaccess aware one should have "other_context"
        $expectedConfig['system']['cro']['design'] = 'standard';
        $expectedConfig['system']['cro']['view']['block_view'] = [
            'some_context' => [
                'block' => [
                    'template' => 'block.html.twig',
                    'match' => [
                        'block_identifier' => 42,
                    ],
                    'parameters' => [
                        'param' => 'value',
                    ],
                ],
            ],
            'other_context' => [
                'block' => [
                    'template' => 'block.html.twig',
                    'match' => [
                        'block_identifier' => 42,
                    ],
                    'parameters' => [
                        'param2' => 'value2',
                    ],
                ],
            ],
        ];

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testViewSettingsRulePositionsWithSystemNodeAndTwoScopes()
    {
        $config = [
            [
                'system' => [
                    'cro' => [
                        'view' => [
                            'block_view' => [
                                'context' => [
                                    'block_three' => [
                                        'template' => 'block.html.twig',
                                        'match' => [
                                            'block_identifier' => 42,
                                        ],
                                        'parameters' => [
                                            'param3' => 'value3',
                                        ],
                                    ],
                                    'block_two' => [
                                        'template' => 'block2.html.twig',
                                        'match' => [
                                            'block_identifier' => 42,
                                        ],
                                        'parameters' => [
                                            'param2' => 'value2',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'default' => [
                        'view' => [
                            'block_view' => [
                                'context' => [
                                    'block_two' => [
                                        'template' => 'block.html.twig',
                                        'match' => [
                                            'block_identifier' => 42,
                                        ],
                                        'parameters' => [
                                            'param2' => 'value2',
                                        ],
                                    ],
                                    'block_one' => [
                                        'template' => 'block.html.twig',
                                        'match' => [
                                            'block_identifier' => 42,
                                        ],
                                        'parameters' => [
                                            'param1' => 'value1',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $processedConfig = $this->processConfig($config);

        // Default scope should only have block_two and block_one
        $this->assertEquals(
            ['block_two', 'block_one'],
            array_keys($processedConfig['system']['default']['view']['block_view']['context'])
        );

        // But only "cro" siteaccess aware one should have all
        // with block_three having priority because it comes from siteaccess scope
        $this->assertEquals(
            ['block_three', 'block_two', 'block_one'],
            array_keys($processedConfig['system']['cro']['view']['block_view']['context'])
        );

        // Rule in "default" scope needs to have the original value
        $this->assertEquals(
            'block.html.twig',
            $processedConfig['system']['default']['view']['block_view']['context']['block_two']['template']
        );

        // Rule in "cro" scope needs to override existing rule in default scope
        $this->assertEquals(
            'block2.html.twig',
            $processedConfig['system']['cro']['view']['block_view']['context']['block_two']['template']
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testViewSettingsRulePositionsWithSystemNodeAndDefaultScope()
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'context' => [
                            'block_two' => [
                                'template' => 'block.html.twig',
                                'match' => [
                                    'block_identifier' => 42,
                                ],
                                'parameters' => [
                                    'param2' => 'value2',
                                ],
                            ],
                            'block_one' => [
                                'template' => 'block.html.twig',
                                'match' => [
                                    'block_identifier' => 42,
                                ],
                                'parameters' => [
                                    'param1' => 'value1',
                                ],
                            ],
                        ],
                    ],
                ],
                'system' => [
                    'default' => [
                        'view' => [
                            'block_view' => [
                                'context' => [
                                    'block_three' => [
                                        'template' => 'block.html.twig',
                                        'match' => [
                                            'block_identifier' => 42,
                                        ],
                                        'parameters' => [
                                            'param3' => 'value3',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $processedConfig = $this->processConfig($config);

        // Default scope should have all three rules,
        // but rule from system node (block_three) should be first
        $this->assertEquals(
            ['block_three', 'block_two', 'block_one'],
            array_keys($processedConfig['system']['default']['view']['block_view']['context'])
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testViewSettingsWithMatchWithArrayValues()
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [
                                'template' => 'block.html.twig',
                                'match' => [24, 42],
                                'parameters' => [
                                    'param' => 'value',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'template' => 'block.html.twig',
                            'match' => [24, 42],
                            'parameters' => [
                                'param' => 'value',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testViewSettingsWithEmptyMatch()
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [
                                'template' => 'block.html.twig',
                                'match' => null,
                                'parameters' => [
                                    'param' => 'value',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'template' => 'block.html.twig',
                            'match' => [],
                            'parameters' => [
                                'param' => 'value',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testViewSettingsWithNoParameters()
    {
        $config = [
            [
                'view' => [
                    'block_view' => [
                        'some_context' => [
                            'block' => [
                                'template' => 'block.html.twig',
                                'match' => [24, 42],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'some_context' => [
                        'block' => [
                            'template' => 'block.html.twig',
                            'match' => [24, 42],
                            'parameters' => [],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testUnknownSettingsAreRemoved()
    {
        $config = [
            [
                'block_types' => [
                    'block' => [
                        'name' => 'Block type',
                    ],
                ],
                'view' => [
                    'block_view' => [
                        'context' => [
                            'block' => [
                                'template' => 'block.html.twig',
                                'match' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'view' => [
                'block_view' => [
                    'context' => [
                        'block' => [
                            'template' => 'block.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);

        $config = $this->plugin->postProcessConfiguration(
            $this->partialProcessor->processConfiguration(
                $this->getConfiguration(),
                null,
                $this->plugin->preProcessConfiguration($config)
            )
        );

        $this->assertEquals($expectedConfig['system']['default'], $config['system']['default']);
    }
}
