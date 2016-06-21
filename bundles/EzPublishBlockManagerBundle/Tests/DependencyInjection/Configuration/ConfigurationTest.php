<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration as BlockManagerConfiguration;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PHPUnit\Framework\TestCase;

abstract class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * Default config here is used because config test library can't test against
     * two or more config tree parts.
     *
     * @var array
     */
    protected $defaultConfig = array(
        'block_definitions' => array(),
        'block_types' => array(),
        'layout_types' => array(),
        'block_type_groups' => array(),
        'block_view' => array(),
        'item_view' => array(),
        'layout_view' => array(),
        'form_view' => array(),
        'rule_target_view' => array(),
        'rule_condition_view' => array(),
        'sources' => array(),
        'query_types' => array(),
        'pagelayout' => 'NetgenBlockManagerBundle::pagelayout.html.twig',
    );

    /**
     * Default config here is used because config test library can't test against
     * two or more config tree parts.
     *
     * @var array
     */
    protected $defaultSystemConfig = array(
        'block_view' => array(),
        'item_view' => array(),
        'layout_view' => array(),
        'form_view' => array(),
        'rule_target_view' => array(),
        'rule_condition_view' => array(),
    );

    /**
     * @var \Closure
     */
    protected $configPreProcessor;

    /**
     * @var \Closure
     */
    protected $configPostProcessor;

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
        $extension = new NetgenEzPublishBlockManagerExtension();
        $this->configPreProcessor = $extension->getPreProcessor();
        $this->configPostProcessor = $extension->getPostProcessor();

        $this->containerBuilderMock = $this->createMock(ContainerBuilder::class);

        $this->partialProcessor = new PartialProcessor();
    }

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        $configuration = new Configuration();
        $blockManagerExtension = new NetgenBlockManagerExtension();

        return new BlockManagerConfiguration(
            $blockManagerExtension->getAlias(),
            array($configuration->getConfigTreeBuilderClosure())
        );
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
        return $expectedConfig + $this->defaultConfig +
            array('system' => array('default' => $expectedConfig + $this->defaultSystemConfig));
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
        $configPreProcessor = $this->configPreProcessor;
        $configPostProcessor = $this->configPostProcessor;

        self::assertEquals(
            $expectedConfig,
            $configPostProcessor(
                $this->partialProcessor->processConfiguration(
                    $this->getConfiguration(),
                    null,
                    $configPreProcessor($config, $this->containerBuilderMock)
                ),
                $this->containerBuilderMock
            )
        );
    }
}
