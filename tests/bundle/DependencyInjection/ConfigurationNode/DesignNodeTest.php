<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

final class DesignNodeTest extends ConfigurationNodeTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testDesignSettings()
    {
        $config = array(
            array(
                'design' => 'standard',
            ),
        );

        $expectedConfig = array(
            'design' => 'standard',
        );

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        $this->assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::validateCurrentDesign
     */
    public function testValidDesign()
    {
        $config = array(
            array(
                'design_list' => array('test1' => array('theme1'), 'test2' => array('theme2')),
                'system' => array(
                    'default' => array(
                        'design' => 'test1',
                    ),
                ),
            ),
        );

        $config = $this->plugin->postProcessConfiguration(
            $this->partialProcessor->processConfiguration(
                $this->getConfiguration(),
                null,
                $this->plugin->preProcessConfiguration($config)
            )
        );

        $this->assertEquals('test1', $config['system']['default']['design']);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::validateCurrentDesign
     */
    public function testStandardDesign()
    {
        $config = array(
            array(
                'design_list' => array('test1' => array('theme1'), 'test2' => array('theme2')),
                'system' => array(
                    'default' => array(
                        'design' => 'standard',
                    ),
                ),
            ),
        );

        $config = $this->plugin->postProcessConfiguration(
            $this->partialProcessor->processConfiguration(
                $this->getConfiguration(),
                null,
                $this->plugin->preProcessConfiguration($config)
            )
        );

        $this->assertEquals('standard', $config['system']['default']['design']);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::validateCurrentDesign
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Design "unknown" does not exist. Available designs are: test1, test2
     */
    public function testInvalidDesignThrowsInvalidConfigurationException()
    {
        $config = array(
            array(
                'design_list' => array('test1' => array('theme1'), 'test2' => array('theme2')),
                'system' => array(
                    'default' => array(
                        'design' => 'unknown',
                    ),
                ),
            ),
        );

        $this->plugin->postProcessConfiguration(
            $this->partialProcessor->processConfiguration(
                $this->getConfiguration(),
                null,
                $this->plugin->preProcessConfiguration($config)
            )
        );
    }
}
