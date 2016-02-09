<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class LayoutsConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testLayoutSettings()
    {
        $config = array(
            array(
                'layouts' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'zone' => array(
                                'name' => 'zone',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_block_types' => array(),
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
    public function testLayoutSettingsWithSystemNode()
    {
        $config = array(
            array(
                'layouts' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'zone' => array(
                                'name' => 'zone',
                            ),
                        ),
                    ),
                ),
                'system' => array(
                    'default' => array(
                        'layouts' => array(
                            'other_layout' => array(
                                'name' => 'other_layout',
                                'zones' => array(
                                    'zone' => array(
                                        'name' => 'zone',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_block_types' => array(),
                        ),
                    ),
                ),
                'other_layout' => array(
                    'name' => 'other_layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_block_types' => array(),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        // Other block group should not appear in original config, but only in siteaccess aware one
        unset($expectedConfig['layouts']['other_layout']);

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testLayoutSettingsNoZonesMerge()
    {
        $config = array(
            array(
                'layouts' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'left' => array(
                                'name' => 'Left',
                            ),
                            'right' => array(
                                'name' => 'Right',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'layouts' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'top' => array(
                                'name' => 'Top',
                            ),
                            'bottom' => array(
                                'name' => 'Bottom',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'top' => array(
                            'name' => 'Top',
                            'allowed_block_types' => array(),
                        ),
                        'bottom' => array(
                            'name' => 'Bottom',
                            'allowed_block_types' => array(),
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
    public function testLayoutSettingsWithAllowedBlockTypes()
    {
        $config = array(
            array(
                'layouts' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'zone' => array(
                                'name' => 'zone',
                                'allowed_block_types' => array('title', 'paragraph'),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_block_types' => array('title', 'paragraph'),
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
    public function testLayoutSettingsWithNonUniqueAllowedBlockTypes()
    {
        $config = array(
            array(
                'layouts' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'zones' => array(
                            'zone' => array(
                                'name' => 'zone',
                                'allowed_block_types' => array('title', 'paragraph', 'title'),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_block_types' => array('title', 'paragraph'),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
