<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

class ViewNodeTest extends ConfigurationNodeTest
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
}
