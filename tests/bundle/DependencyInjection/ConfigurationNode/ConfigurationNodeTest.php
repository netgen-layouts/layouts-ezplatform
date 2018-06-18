<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class ConfigurationNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension
     */
    protected $extension;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin
     */
    protected $plugin;

    /**
     * @var \Matthias\SymfonyConfigTest\Partial\PartialProcessor
     */
    protected $partialProcessor;

    /**
     * Default config here is used because config test library can't test against
     * two or more config tree parts.
     *
     * @var array
     */
    private static $defaultSystemConfig = [
        'view' => [],
        'design' => 'standard',
    ];

    public function setUp(): void
    {
        $this->plugin = new ExtensionPlugin(new ContainerBuilder());

        $this->extension = new NetgenBlockManagerExtension();
        $this->extension->addPlugin($this->plugin);

        $this->partialProcessor = new PartialProcessor();
    }

    /**
     * Asserts that processed $config is equal to $expectedConfig, after being
     * processed by preprocessors and postprocessors.
     */
    public function assertInjectedConfigurationEqual(array $expectedConfig, array $config): void
    {
        $actualConfig = $this->processConfig($config);

        foreach ($actualConfig as $key => $value) {
            if ($key !== 'system' && !array_key_exists($key, self::$defaultSystemConfig)) {
                unset($actualConfig[$key]);
            }
        }

        ksort($expectedConfig);
        ksort($actualConfig);

        ksort($expectedConfig['system']);
        ksort($actualConfig['system']);

        $this->assertSame($expectedConfig, $actualConfig);
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
     */
    protected function getExtendedExpectedConfig(array $expectedConfig): array
    {
        return $expectedConfig + [
            'system' => [
                'default' => $expectedConfig + self::$defaultSystemConfig,
            ],
        ] + self::$defaultSystemConfig;
    }

    /**
     * Processes the extension config by using pre and post processors.
     */
    protected function processConfig(array $config): array
    {
        return $this->plugin->postProcessConfiguration(
            $this->partialProcessor->processConfiguration(
                $this->getConfiguration(),
                null,
                $this->plugin->preProcessConfiguration($config)
            )
        );
    }
}
