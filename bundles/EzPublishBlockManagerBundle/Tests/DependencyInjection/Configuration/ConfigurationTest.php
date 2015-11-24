<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration as BlockManagerConfiguration;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Matthias\SymfonyConfigTest\Partial\PartialProcessor;

abstract class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * Default config here is used because config test library can't test against
     * two or more config tree parts.
     *
     * @var array
     */
    protected $defaultConfig = array(
        'blocks' => array(),
        'layouts' => array(),
        'block_groups' => array(),
        'block_view' => array(),
        'layout_view' => array(),
        'pagelayout' => 'NetgenBlockManagerBundle::pagelayout_empty.html.twig',
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
     * Sets up the test
     */
    public function setUp()
    {
        $extension = new NetgenEzPublishBlockManagerExtension();
        $this->configPreProcessor = $extension->getPreProcessor();
        $this->configPostProcessor = $extension->getPostProcessor();

        $this->containerBuilderMock = $this->getMock(
            'Symfony\Component\DependencyInjection\ContainerBuilder'
        );

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
     * Asserts that processed $config is equal to $expectedConfig, after being
     * processed by preprocessors and postprocessors
     *
     * @param $expectedConfig
     * @param $config
     */
    public function assertInjectedConfigurationEqual($expectedConfig, $config)
    {
        $expectedConfig = $expectedConfig + $this->defaultConfig +
            array('system' => array('default' => $expectedConfig + $this->defaultConfig));

        $configPreProcessor = $this->configPreProcessor;
        $configPostProcessor = $this->configPostProcessor;

        self::assertEquals(
            $configPostProcessor($expectedConfig, $this->containerBuilderMock),
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
