<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class BlockTypesConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockTypeSettings()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'type_name' => 'Block type',
                        'defaults' => array(
                            'name' => 'Name',
                            'definition_identifier' => 'title',
                            'view_type' => 'large',
                            'parameters' => array(
                                'param1' => 'value1',
                                'param2' => 'value2',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'name' => 'Name',
                        'definition_identifier' => 'title',
                        'view_type' => 'large',
                            'parameters' => array(
                                'param1' => 'value1',
                                'param2' => 'value2',
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
    public function testBlockTypeSettingsWithSystemNode()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'type_name' => 'Block type',
                        'defaults' => array(
                            'name' => 'Name',
                            'definition_identifier' => 'title',
                            'view_type' => 'large',
                            'parameters' => array(
                                'param1' => 'value1',
                                'param2' => 'value2',
                            ),
                        ),
                    ),
                ),
                'system' => array(
                    'default' => array(
                        'block_types' => array(
                            'other_block_type' => array(
                                'type_name' => 'Other block type',
                                'defaults' => array(
                                    'name' => 'Name',
                                    'definition_identifier' => 'title',
                                    'view_type' => 'large',
                                    'parameters' => array(
                                        'param1' => 'value1',
                                        'param2' => 'value2',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'name' => 'Name',
                        'definition_identifier' => 'title',
                        'view_type' => 'large',
                            'parameters' => array(
                                'param1' => 'value1',
                                'param2' => 'value2',
                            ),
                    ),
                ),
                'other_block_type' => array(
                    'type_name' => 'Other block type',
                    'defaults' => array(
                        'name' => 'Name',
                        'definition_identifier' => 'title',
                        'view_type' => 'large',
                            'parameters' => array(
                                'param1' => 'value1',
                                'param2' => 'value2',
                            ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        // Other block type should not appear in original config, but only in siteaccess aware one
        unset($expectedConfig['block_types']['other_block_type']);

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockTypeSettingsWithNoDefaultName()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'type_name' => 'Block type',
                        'defaults' => array(
                            'definition_identifier' => 'title',
                            'view_type' => 'large',
                            'parameters' => array(
                                'param1' => 'value1',
                                'param2' => 'value2',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'name' => '',
                        'definition_identifier' => 'title',
                        'view_type' => 'large',
                        'parameters' => array(
                            'param1' => 'value1',
                            'param2' => 'value2',
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
    public function testBlockTypeSettingsWithNoDefaultParameters()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'type_name' => 'Block type',
                        'defaults' => array(
                            'name' => 'Name',
                            'definition_identifier' => 'title',
                            'view_type' => 'large',
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'name' => 'Name',
                        'definition_identifier' => 'title',
                        'view_type' => 'large',
                        'parameters' => array(),
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
    public function testBlockTypeSettingsWithNoParametersMerge()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'type_name' => 'Block type',
                        'defaults' => array(
                            'name' => 'Name',
                            'definition_identifier' => 'title',
                            'view_type' => 'large',
                            'parameters' => array(
                                'param1' => 'value1',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'block_types' => array(
                    'block_type' => array(
                        'type_name' => 'Block type',
                        'defaults' => array(
                            'name' => 'Name',
                            'definition_identifier' => 'title',
                            'view_type' => 'large',
                            'parameters' => array(
                                'param2' => 'value2',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'type_name' => 'Block type',
                    'defaults' => array(
                        'name' => 'Name',
                        'definition_identifier' => 'title',
                        'view_type' => 'large',
                        'parameters' => array(
                            'param2' => 'value2',
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
