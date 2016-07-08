<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class ViewConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testViewSettings()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
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
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
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

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testViewSettingsWithSystemNodeAndDefaultScope()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
                                'match' => array(
                                    'block_identifier' => 42,
                                ),
                            ),
                        ),
                    ),
                ),
                'system' => array(
                    'default' => array(
                        'view' => array(
                            'block_view' => array(
                                'other_context' => array(
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
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(
                                'block_identifier' => 42,
                            ),
                        ),
                    ),
                    'other_context' => array(
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

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        // other_context context should not appear in original config, but only in siteaccess aware one
        unset($expectedConfig['view']['block_view']['other_context']);

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testViewSettingsWithSystemNodeAndNonDefaultScope()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
                                'match' => array(
                                    'block_identifier' => 42,
                                ),
                            ),
                        ),
                    ),
                ),
                'system' => array(
                    'cro' => array(
                        'view' => array(
                            'block_view' => array(
                                'other_context' => array(
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
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
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

        // Both original and siteaccess aware config should have "some_context"
        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);

        // But only "cro" siteaccess aware one should have "other_context"
        $expectedConfig['system']['cro']['view']['block_view'] = array(
            'other_context' => array(
                'block' => array(
                    'template' => 'block.html.twig',
                    'match' => array(
                        'block_identifier' => 42,
                    ),
                ),
            ),
        );

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testViewSettingsWithMatchWithArrayValues()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
                                'match' => array(24, 42),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(24, 42),
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
    public function testViewSettingsWithEmptyMatch()
    {
        $config = array(
            array(
                'view' => array(
                    'block_view' => array(
                        'some_context' => array(
                            'block' => array(
                                'template' => 'block.html.twig',
                                'match' => null,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'view' => array(
                'block_view' => array(
                    'some_context' => array(
                        'block' => array(
                            'template' => 'block.html.twig',
                            'match' => array(),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
