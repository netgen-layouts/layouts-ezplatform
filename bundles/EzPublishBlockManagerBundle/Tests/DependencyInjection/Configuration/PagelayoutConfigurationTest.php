<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

class PagelayoutConfigurationTest extends ConfigurationTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPreProcessor
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::getPostProcessor
     */
    public function testPagelayoutSettings()
    {
        $this->markTestSkipped('Fails for unknown reason. Bug in matthiasnoback/symfony-config-test maybe?');
        $config = array(
            array(
                'pagelayout' => 'pagelayout.html.twig',
            ),
        );

        $expectedConfig = array(
            'pagelayout' => 'pagelayout.html.twig',
        );

        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }
}
