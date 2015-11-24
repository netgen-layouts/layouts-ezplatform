<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class TemplateResolverConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testTemplateResolverSettings()
    {
        $config = array(
            array(
                'block_view' => array(
                    'api' => array(
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
                'api' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(
                            'block_identifier' => 42,
                        ),
                    ),
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
    public function testTemplateResolverSettingsWithMatchWithArrayValues()
    {
        $config = array(
            array(
                'block_view' => array(
                    'api' => array(
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
                'api' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(24, 42),
                    ),
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
    public function testTemplateResolverSettingsWithEmptyMatch()
    {
        $config = array(
            array(
                'block_view' => array(
                    'api' => array(
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
                'api' => array(
                    'block' => array(
                        'template' => 'block.html.twig',
                        'match' => array(),
                    ),
                ),
            ),
        );

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
