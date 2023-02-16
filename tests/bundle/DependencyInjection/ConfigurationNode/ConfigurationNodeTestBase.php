<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function array_key_exists;
use function ksort;

abstract class ConfigurationNodeTestBase extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * Default configs here are used because config test library can't test against
     * two or more config tree parts.
     */
    private const DEFAULT_CONFIG = [
        'view' => [],
        'design' => 'standard',
    ];

    private const DEFAULT_SYSTEM_CONFIG = [
        'view' => [],
        'design' => 'standard',
        'ezcomponent' => [
            'default_parent_location' => 2,
            'parent_locations' => [],
        ],
    ];

    protected NetgenLayoutsExtension $extension;

    protected ExtensionPlugin $plugin;

    protected PartialProcessor $partialProcessor;

    protected function setUp(): void
    {
        $this->extension = new NetgenLayoutsExtension();
        $this->plugin = new ExtensionPlugin(new ContainerBuilder(), $this->extension);

        $this->extension->addPlugin($this->plugin);

        $this->partialProcessor = new PartialProcessor();
    }

    /**
     * Asserts that processed $config is equal to $expectedConfig, after being
     * processed by preprocessors and postprocessors.
     *
     * @param mixed[] $expectedConfig
     * @param mixed[] $config
     */
    public function assertInjectedConfigurationEqual(array $expectedConfig, array $config): void
    {
        $actualConfig = $this->processConfig($config);

        foreach ($actualConfig as $key => $value) {
            if ($key !== 'system' && !array_key_exists($key, self::DEFAULT_SYSTEM_CONFIG)) {
                unset($actualConfig[$key]);
            }
        }

        ksort($expectedConfig);
        ksort($actualConfig);

        ksort($expectedConfig['system']);
        ksort($actualConfig['system']);

        self::assertSame($expectedConfig, $actualConfig);
    }

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     */
    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration($this->extension);
    }

    /**
     * Returns the expected config, extended with 'system' node.
     *
     * @param mixed[] $expectedConfig
     *
     * @return mixed[]
     */
    protected function getExtendedExpectedConfig(array $expectedConfig): array
    {
        return $expectedConfig + [
            'system' => [
                'default' => $expectedConfig + self::DEFAULT_SYSTEM_CONFIG,
            ],
        ] + self::DEFAULT_CONFIG;
    }

    /**
     * Processes the extension config by using pre and post processors.
     *
     * @param mixed[] $config
     *
     * @return mixed[]
     */
    protected function processConfig(array $config): array
    {
        return $this->plugin->postProcessConfiguration(
            $this->partialProcessor->processConfiguration(
                $this->getConfiguration(),
                null,
                $this->plugin->preProcessConfiguration($config),
            ),
        );
    }
}
