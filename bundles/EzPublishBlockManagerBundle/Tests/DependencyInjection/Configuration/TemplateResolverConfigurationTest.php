<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class TemplateResolverConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testTemplateResolverSettings()
    {
        $config = array(
            array(
                'block_view' => array(
                    'view' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(
                                'block_identifier' => 42,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_view' => array(
                'view' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(
                            'block_identifier' => 42,
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
    public function testTemplateResolverSettingsWithSystemNode()
    {
        $config = array(
            array(
                'block_view' => array(
                    'view' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(
                                'block_identifier' => 42,
                            ),
                        ),
                    ),
                ),
                'system' => array(
                    'default' => array(
                        'block_view' => array(
                            'other_view' => array(
                                'block' => array(
                                    'template' => 'block.html.twig',
                                    'match' => array(
                                        'block_identifier' => 42,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_view' => array(
                'view' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(
                            'block_identifier' => 42,
                        ),
                    ),
                ),
                'other_view' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(
                            'block_identifier' => 42,
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        // other_view context should not appear in original config, but only in siteaccess aware one
        unset($expectedConfig['block_view']['other_view']);

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testTemplateResolverSettingsWithMatchWithArrayValues()
    {
        $config = array(
            array(
                'block_view' => array(
                    'view' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(24, 42),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_view' => array(
                'view' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(24, 42),
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
    public function testTemplateResolverSettingsWithEmptyMatch()
    {
        $config = array(
            array(
                'block_view' => array(
                    'view' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => null,
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_view' => array(
                'view' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
