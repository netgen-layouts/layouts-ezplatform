<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class SourcesConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testSourceSettings()
    {
        $config = array(
            array(
                'sources' => array(
                    'dynamic' => array(
                        'name' => 'Dynamic',
                        'queries' => array(
                            'default' => array(
                                'query_type' => 'type',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'sources' => array(
                'dynamic' => array(
                    'name' => 'Dynamic',
                    'queries' => array(
                        'default' => array(
                            'query_type' => 'type',
                            'default_parameters' => array(),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testSourceSettingsWithSystemNode()
    {
        $config = array(
            array(
                'sources' => array(
                    'dynamic' => array(
                        'name' => 'Dynamic',
                        'queries' => array(
                            'default' => array(
                                'query_type' => 'type',
                            ),
                        ),
                    ),
                ),
                'system' => array(
                    'default' => array(
                        'sources' => array(
                            'other_source' => array(
                                'name' => 'Other source',
                                'queries' => array(
                                    'default' => array(
                                        'query_type' => 'type',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'sources' => array(
                'dynamic' => array(
                    'name' => 'Dynamic',
                    'queries' => array(
                        'default' => array(
                            'query_type' => 'type',
                            'default_parameters' => array(),
                        ),
                    ),
                ),
                'other_source' => array(
                    'name' => 'Other source',
                    'queries' => array(
                        'default' => array(
                            'query_type' => 'type',
                            'default_parameters' => array(),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        // Other block group should not appear in original config, but only in siteaccess aware one
        unset($expectedConfig['sources']['other_source']);

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testSourceSettingsNoQueriesMerge()
    {
        $config = array(
            array(
                'sources' => array(
                    'dynamic' => array(
                        'name' => 'Dynamic',
                        'queries' => array(
                            'default' => array(
                                'query_type' => 'type',
                                'default_parameters' => array(
                                    'param' => 'value',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'sources' => array(
                    'dynamic' => array(
                        'name' => 'Dynamic',
                        'queries' => array(
                            'default' => array(
                                'query_type' => 'type2',
                                'default_parameters' => array(
                                    'param2' => 'value2',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'sources' => array(
                'dynamic' => array(
                    'name' => 'Dynamic',
                    'queries' => array(
                        'default' => array(
                            'query_type' => 'type2',
                            'default_parameters' => array(
                                'param2' => 'value2',
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testSourceSettingsWithDefaultParameters()
    {
        $config = array(
            array(
                'sources' => array(
                    'dynamic' => array(
                        'name' => 'Dynamic',
                        'queries' => array(
                            'default' => array(
                                'query_type' => 'type',
                                'default_parameters' => array(
                                    'param' => 'value',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'sources' => array(
                'dynamic' => array(
                    'name' => 'Dynamic',
                    'queries' => array(
                        'default' => array(
                            'query_type' => 'type',
                            'default_parameters' => array(
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
}
