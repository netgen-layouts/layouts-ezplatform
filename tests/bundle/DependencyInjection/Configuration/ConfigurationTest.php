<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * Default config here is used because config test library can't test against
     * two or more config tree parts.
     *
     * @var array
     */
    const DEFAULT_CONFIG = array(
        'block_definitions' => array(),
        'block_types' => array(),
        'layout_types' => array(),
        'block_type_groups' => array(),
        'view' => array(),
        'sources' => array(),
        'query_types' => array(),
        'pagelayout' => 'NetgenBlockManagerBundle::empty_pagelayout.html.twig',
        'google_maps_api_key' => '',
        'default_view_templates' => array(),
    );

    /**
     * Default config here is used because config test library can't test against
     * two or more config tree parts.
     *
     * @var array
     */
    const DEFAULT_SYSTEM_CONFIG = array(
        'view' => array(),
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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $containerBuilderMock;

    /**
     * @var \Matthias\SymfonyConfigTest\Partial\PartialProcessor
     */
    protected $partialProcessor;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->containerBuilderMock = $this->createMock(ContainerBuilder::class);

        $this->plugin = new ExtensionPlugin($this->containerBuilderMock);

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
        $containerBuilder = new ContainerBuilder();

        $this->assertEquals(
            $expectedConfig,
            $this->plugin->postProcessConfiguration(
                $this->partialProcessor->processConfiguration(
                    $this->getConfiguration(),
                    null,
                    $this->plugin->preProcessConfiguration($config, $containerBuilder)
                ),
                $containerBuilder
            )
        );
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
        return $expectedConfig + self::DEFAULT_CONFIG +
            array('system' => array('default' => $expectedConfig + self::DEFAULT_SYSTEM_CONFIG));
    }
}
