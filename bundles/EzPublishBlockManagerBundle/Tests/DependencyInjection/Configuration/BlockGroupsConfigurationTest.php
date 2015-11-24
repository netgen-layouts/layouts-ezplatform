<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class BlockGroupsConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
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

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockGroupsSettingsWithSystemNode()
    {
        $config = array(
            array(
                'block_groups' => array(
                    'block_group' => array(
                        'name' => 'block_group',
                    ),
                ),
                'system' => array(
                    'default' => array(
                        'block_groups' => array(
                            'other_block_group' => array(
                                'name' => 'other_block_group',
                            ),
                        ),
                    )
                )
            ),
        );

        $expectedConfig = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array(),
                ),
                'other_block_group' => array(
                    'name' => 'other_block_group',
                    'blocks' => array(),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        // Other block group should not appear in original config, but only in siteaccess aware one
        unset($expectedConfig['block_groups']['other_block_group']);

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
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

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
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

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
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

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
