<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class BlocksConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockSettings()
    {
        $config = array(
            array(
                'blocks' => array(
                    'block' => array(
                        'name' => 'block',
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
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                        ),
                        'large' => array(
                            'name' => 'Large',
                        ),
                    ),
                ),
            )
        );

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testBlockSettingsNoViewTypesMerge()
    {
        $config = array(
            array(
                'blocks' => array(
                    'block' => array(
                        'name' => 'block',
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
                'blocks' => array(
                    'block' => array(
                        'name' => 'block',
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
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array(
                        'title' => array(
                            'name' => 'Title',
                        ),
                        'image' => array(
                            'name' => 'Image',
                        ),
                    ),
                ),
            )
        );

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
