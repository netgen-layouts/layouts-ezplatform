<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class PagelayoutConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testPagelayoutSettings()
    {
        $config = array(
            array(
                'pagelayout' => 'pagelayout.html.twig',
            ),
        );

        $expectedConfig = array(
            'pagelayout' => 'pagelayout.html.twig',
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilderClosure
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testPagelayoutSettingsWithSystemConfig()
    {
        $config = array(
            array(
                'pagelayout' => 'pagelayout.html.twig',
                'system' => array(
                    'default' => array(
                        'pagelayout' => 'other_pagelayout.html.twig',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'pagelayout' => 'other_pagelayout.html.twig',
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $expectedConfig['pagelayout'] = 'pagelayout.html.twig';

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
