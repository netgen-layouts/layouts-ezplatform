<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class BlockTypeGroupsConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockTypeGroupsSettings()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'block_types' => array(),
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
    public function testBlockTypeGroupsSettingsWithSystemNode()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                    ),
                ),
                'system' => array(
                    'default' => array(
                        'block_type_groups' => array(
                            'other_block_type_group' => array(
                                'name' => 'other_block_type_group',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'block_types' => array(),
                ),
                'other_block_type_group' => array(
                    'name' => 'other_block_type_group',
                    'block_types' => array(),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        // Other block group should not appear in original config, but only in siteaccess aware one
        unset($expectedConfig['block_type_groups']['other_block_type_group']);

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockTypeGroupsSettingsWithBlockTypesMerge()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                        'block_types' => array('title', 'paragraph'),
                    ),
                ),
            ),
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                        'block_types' => array('image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'block_types' => array('title', 'paragraph', 'image'),
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
    public function testBlockTypeGroupsSettingsWithBlockTypes()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                        'block_types' => array('title', 'image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'block_types' => array('title', 'image'),
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
    public function testBlockTypeGroupsSettingsWithNonUniqueBlockTypes()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                        'block_types' => array('title', 'image', 'title'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'block_types' => array('title', 'image'),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
