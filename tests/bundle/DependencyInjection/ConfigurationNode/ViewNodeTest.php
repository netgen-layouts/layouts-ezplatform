<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

final class ViewNodeTest extends ConfigurationNodeTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     */
    public function testViewSettings()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
                                'match' => array(
                                    'block_identifier' => 42,
                                ),
                                'parameters' => array(
                                    'param' => 'value',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(
                                'block_identifier' => 42,
                            ),
                            'parameters' => array(
                                'param' => 'value',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     */
    public function testDesignSettings()
    {
        $config = array(
            array(
                'design' => 'standard',
            ),
        );

        $expectedConfig = array(
            'design' => 'standard',
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     */
    public function testViewSettingsWithSystemNodeAndDefaultScope()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
                                'match' => array(
                                    'block_identifier' => 42,
                                ),
                                'parameters' => array(
                                    'param' => 'value',
                                ),
                            ),
                        ),
                    ),
                ),
                'system' => array(
                    'default' => array(
                        'view' => array(
                            'block_view' => array(
                                'other_context' => array(
                                    'block' => array(
                                        'template' => 'block.html.twig',
                                        'match' => array(
                                            'block_identifier' => 42,
                                        ),
                                        'parameters' => array(
                                            'param2' => 'value2',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(
                                'block_identifier' => 42,
                            ),
                            'parameters' => array(
                                'param' => 'value',
                            ),
                        ),
                    ),
                    'other_context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(
                                'block_identifier' => 42,
                            ),
                            'parameters' => array(
                                'param2' => 'value2',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        // other_context context should not appear in original config, but only in siteaccess aware one
        unset($expectedConfig['view']['block_view']['other_context']);

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     */
    public function testViewSettingsWithSystemNodeAndNonDefaultScope()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
                                'match' => array(
                                    'block_identifier' => 42,
                                ),
                                'parameters' => array(
                                    'param' => 'value',
                                ),
                            ),
                        ),
                    ),
                ),
                'system' => array(
                    'cro' => array(
                        'view' => array(
                            'block_view' => array(
                                'other_context' => array(
                                    'block' => array(
                                        'template' => 'block.html.twig',
                                        'match' => array(
                                            'block_identifier' => 42,
                                        ),
                                        'parameters' => array(
                                            'param2' => 'value2',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(
                                'block_identifier' => 42,
                            ),
                            'parameters' => array(
                                'param' => 'value',
                            ),
                        ),
                    ),
                ),
            ),
        );

        // All configs should have "some_context"
        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);

        // But only "cro" siteaccess aware one should have "other_context"
        $expectedConfig['system']['cro']['design'] = 'standard';
        $expectedConfig['system']['cro']['view']['block_view'] = array(
            'some_context' => array(
                'block' => array(
                    'template' => 'block.html.twig',
                    'match' => array(
                        'block_identifier' => 42,
                    ),
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
            ),
            'other_context' => array(
                'block' => array(
                    'template' => 'block.html.twig',
                    'match' => array(
                        'block_identifier' => 42,
                    ),
                    'parameters' => array(
                        'param2' => 'value2',
                    ),
                ),
            ),
        );

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     */
    public function testViewSettingsRulePositionsWithSystemNodeAndTwoScopes()
    {
        $config = array(
            array(
                'system' => array(
                    'cro' => array(
                        'view' => array(
                            'block_view' => array(
                                'context' => array(
                                    'block_three' => array(
                                        'template' => 'block.html.twig',
                                        'match' => array(
                                            'block_identifier' => 42,
                                        ),
                                        'parameters' => array(
                                            'param3' => 'value3',
                                        ),
                                    ),
                                    'block_two' => array(
                                        'template' => 'block2.html.twig',
                                        'match' => array(
                                            'block_identifier' => 42,
                                        ),
                                        'parameters' => array(
                                            'param2' => 'value2',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'default' => array(
                        'view' => array(
                            'block_view' => array(
                                'context' => array(
                                    'block_two' => array(
                                        'template' => 'block.html.twig',
                                        'match' => array(
                                            'block_identifier' => 42,
                                        ),
                                        'parameters' => array(
                                            'param2' => 'value2',
                                        ),
                                    ),
                                    'block_one' => array(
                                        'template' => 'block.html.twig',
                                        'match' => array(
                                            'block_identifier' => 42,
                                        ),
                                        'parameters' => array(
                                            'param1' => 'value1',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $processedConfig = $this->processConfig($config);

        // Default scope should only have block_two and block_one
        $this->assertEquals(
            array('block_two', 'block_one'),
            array_keys($processedConfig['system']['default']['view']['block_view']['context'])
        );

        // But only "cro" siteaccess aware one should have all
        // with block_three having priority because it comes from siteaccess scope
        $this->assertEquals(
            array('block_three', 'block_two', 'block_one'),
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     */
    public function testViewSettingsRulePositionsWithSystemNodeAndDefaultScope()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'context' => array(
                            'block_two' => array(
                                'template' => 'block.html.twig',
                                'match' => array(
                                    'block_identifier' => 42,
                                ),
                                'parameters' => array(
                                    'param2' => 'value2',
                                ),
                            ),
                            'block_one' => array(
                                'template' => 'block.html.twig',
                                'match' => array(
                                    'block_identifier' => 42,
                                ),
                                'parameters' => array(
                                    'param1' => 'value1',
                                ),
                            ),
                        ),
                    ),
                ),
                'system' => array(
                    'default' => array(
                        'view' => array(
                            'block_view' => array(
                                'context' => array(
                                    'block_three' => array(
                                        'template' => 'block.html.twig',
                                        'match' => array(
                                            'block_identifier' => 42,
                                        ),
                                        'parameters' => array(
                                            'param3' => 'value3',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $processedConfig = $this->processConfig($config);

        // Default scope should have all three rules,
        // but rule from system node (block_three) should be first
        $this->assertEquals(
            array('block_three', 'block_two', 'block_one'),
            array_keys($processedConfig['system']['default']['view']['block_view']['context'])
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     */
    public function testViewSettingsWithMatchWithArrayValues()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
                                'match' => array(24, 42),
                                'parameters' => array(
                                    'param' => 'value',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(24, 42),
                            'parameters' => array(
                                'param' => 'value',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     */
    public function testViewSettingsWithEmptyMatch()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
                                'match' => null,
                                'parameters' => array(
                                    'param' => 'value',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(),
                            'parameters' => array(
                                'param' => 'value',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::fixUpViewConfig
     */
    public function testViewSettingsWithNoParameters()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
                                'match' => array(24, 42),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(24, 42),
                            'parameters' => array(),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     */
    public function testUnknownSettingsAreRemoved()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block' => array(
                        'name' => 'Block type',
                    ),
                ),
                'view' => array(
                    'block_view' => array(
                        'context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
                                'match' => array(),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(),
                            'parameters' => array(),
                        ),
                    ),
                ),
            ),
        );

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
