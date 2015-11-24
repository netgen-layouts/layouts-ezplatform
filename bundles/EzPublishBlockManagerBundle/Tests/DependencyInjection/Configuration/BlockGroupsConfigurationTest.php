<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class BlockGroupsConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockGroupsSettings()
    {
        $config = array(
            array(
                'block_groups' => array(
                    'block_group' => array(
                        'name' => 'block_group',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array(),
                ),
            ),
        );

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockGroupsSettingsWithBlocksMerge()
    {
        $config = array(
            array(
                'block_groups' => array(
                    'block_group' => array(
                        'name' => 'block_group',
                        'blocks' => array('title', 'paragraph'),
                    ),
                ),
            ),
            array(
                'block_groups' => array(
                    'block_group' => array(
                        'name' => 'block_group',
                        'blocks' => array('image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('title', 'paragraph', 'image'),
                ),
            ),
        );

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockGroupsSettingsWithBlocks()
    {
        $config = array(
            array(
                'block_groups' => array(
                    'block_group' => array(
                        'name' => 'block_group',
                        'blocks' => array('title', 'image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('title', 'image'),
                ),
            ),
        );

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockGroupsSettingsWithNonUniqueBlocks()
    {
        $config = array(
            array(
                'block_groups' => array(
                    'block_group' => array(
                        'name' => 'block_group',
                        'blocks' => array('title', 'image', 'title'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('title', 'image'),
                ),
            ),
        );

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
