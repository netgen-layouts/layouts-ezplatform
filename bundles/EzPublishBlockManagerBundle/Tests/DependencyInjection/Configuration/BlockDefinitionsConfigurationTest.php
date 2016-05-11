<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class BlockDefinitionsConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockDefinitionSettings()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'forms' => array(
                            'edit' => 'block_edit',
                        ),
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                            ),
                            'large' => array(
                                'name' => 'Large',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'edit' => 'block_edit',
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                        ),
                        'large' => array(
                            'name' => 'Large',
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     */
    public function testBlockDefinitionSettingsWithNoFullForm()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'forms' => array(),
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                            ),
                            'large' => array(
                                'name' => 'Large',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'edit' => 'block_edit',
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                        ),
                        'large' => array(
                            'name' => 'Large',
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
    public function testBlockDefinitionSettingsWithSystemNode()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'forms' => array(
                            'edit' => 'block_edit',
                        ),
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                            ),
                            'large' => array(
                                'name' => 'Large',
                            ),
                        ),
                    ),
                ),
                'system' => array(
                    'default' => array(
                        'block_definitions' => array(
                            'other_block' => array(
                                'forms' => array(
                                    'edit' => 'block_edit',
                                ),
                                'view_types' => array(
                                    'small' => array(
                                        'name' => 'Small',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'edit' => 'block_edit',
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                        ),
                        'large' => array(
                            'name' => 'Large',
                        ),
                    ),
                ),
                'other_block' => array(
                    'forms' => array(
                        'edit' => 'block_edit',
                    ),
                    'view_types' => array(
                        'small' => array(
                            'name' => 'Small',
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        // Other block should not appear in original config, but only in siteaccess aware one
        unset($expectedConfig['block_definitions']['other_block']);

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockDefinitionSettingsNoViewTypesMerge()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'forms' => array(
                            'edit' => 'block_edit',
                        ),
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                            ),
                            'large' => array(
                                'name' => 'Large',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'block_definitions' => array(
                    'block' => array(
                        'forms' => array(
                            'edit' => 'block_edit',
                        ),
                        'view_types' => array(
                            'title' => array(
                                'name' => 'Title',
                            ),
                            'image' => array(
                                'name' => 'Image',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'edit' => 'block_edit',
                    ),
                    'view_types' => array(
                        'title' => array(
                            'name' => 'Title',
                        ),
                        'image' => array(
                            'name' => 'Image',
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
