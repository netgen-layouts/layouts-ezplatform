<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Tests\DependencyInjection\ConfigurationNode;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

final class DesignNodeTest extends ConfigurationNodeTestBase
{
    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testDesignSettings(): void
    {
        $config = [
            [
                'design' => 'standard',
            ],
        ];

        $expectedConfig = [
            'design' => 'standard',
        ];

        $expectedConfig = $this->getExtendedExpectedConfig($expectedConfig);
        self::assertInjectedConfigurationEqual($expectedConfig, $config);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::validateCurrentDesign
     */
    public function testValidDesign(): void
    {
        $config = [
            [
                'design_list' => ['test1' => ['theme1'], 'test2' => ['theme2']],
                'system' => [
                    'default' => [
                        'design' => 'test1',
                    ],
                ],
            ],
        ];

        $config = $this->plugin->postProcessConfiguration(
            $this->partialProcessor->processConfiguration(
                $this->getConfiguration(),
                null,
                $this->plugin->preProcessConfiguration($config),
            ),
        );

        self::assertSame('test1', $config['system']['default']['design']);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::validateCurrentDesign
     */
    public function testStandardDesign(): void
    {
        $config = [
            [
                'design_list' => ['test1' => ['theme1'], 'test2' => ['theme2']],
                'system' => [
                    'default' => [
                        'design' => 'standard',
                    ],
                ],
            ],
        ];

        $config = $this->plugin->postProcessConfiguration(
            $this->partialProcessor->processConfiguration(
                $this->getConfiguration(),
                null,
                $this->plugin->preProcessConfiguration($config),
            ),
        );

        self::assertSame('standard', $config['system']['default']['design']);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::validateCurrentDesign
     */
    public function testInvalidDesignThrowsInvalidConfigurationException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Design "unknown" does not exist. Available designs are: test1, test2');

        $config = [
            [
                'design_list' => ['test1' => ['theme1'], 'test2' => ['theme2']],
                'system' => [
                    'default' => [
                        'design' => 'unknown',
                    ],
                ],
            ],
        ];

        $this->plugin->postProcessConfiguration(
            $this->partialProcessor->processConfiguration(
                $this->getConfiguration(),
                null,
                $this->plugin->preProcessConfiguration($config),
            ),
        );
    }
}
