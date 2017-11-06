<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class ConfigurationNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * Default config here is used because config test library can't test against
     * two or more config tree parts.
     *
     * @var array
     */
    const DEFAULT_SYSTEM_CONFIG = array(
        'view' => array(),
        'design' => 'standard',
    );

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
     * Sets up the test.
     */
    public function setUp()
    {
        $this->plugin = new ExtensionPlugin(new ContainerBuilder());

        $this->extension = new NetgenBlockManagerExtension();
        $this->extension->addPlugin($this->plugin);

        $this->partialProcessor = new PartialProcessor();
    }

    /**
     * Asserts that processed $config is equal to $expectedConfig, after being
     * processed by preprocessors and postprocessors.
     *
     * @param array $expectedConfig
     * @param array $config
     */
    public function assertInjectedConfigurationEqual(array $expectedConfig, array $config)
    {
        $actualConfig = $this->processConfig($config);

        foreach ($actualConfig as $key => $value) {
            if ($key !== 'system' && !array_key_exists($key, self::DEFAULT_SYSTEM_CONFIG)) {
                unset($actualConfig[$key]);
            }
        }

        $this->assertEquals($expectedConfig, $actualConfig);
    }

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration($this->extension);
    }

    /**
     * Returns the expected config, extended with 'system' node.
     *
     * @param array $expectedConfig
     *
     * @return array
     */
    protected function getExtendedExpectedConfig(array $expectedConfig)
    {
        return $expectedConfig + self::DEFAULT_SYSTEM_CONFIG + array(
            'system' => array(
                'default' => $expectedConfig + self::DEFAULT_SYSTEM_CONFIG,
            ),
        );
    }

    /**
     * Processes the extension config by using pre and post processors.
     *
     * @param array $config
     *
     * @return array
     */
    protected function processConfig(array $config)
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
