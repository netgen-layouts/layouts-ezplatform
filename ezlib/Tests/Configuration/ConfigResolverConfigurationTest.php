<?php

namespace Netgen\BlockManager\Ez\Tests\Configuration;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use PHPUnit\Framework\TestCase;

class ConfigResolverConfigurationTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fallbackConfigurationMock;

    /**
     * @var \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration
     */
    protected $configuration;

    /**
     * Sets up the tests.
     */
    protected function setUp()
    {
        if (!interface_exists('eZ\Publish\Core\MVC\ConfigResolverInterface')) {
            $this->markTestSkipped('No eZ Publish installed, ConfigResolverConfiguration tests skipped.');
        }

        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $this->fallbackConfigurationMock = $this->createMock(ConfigurationInterface::class);

        $this->configuration = new ConfigResolverConfiguration($this->fallbackConfigurationMock);
        $this->configuration->setConfigResolver($this->configResolverMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::__construct
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameter()
    {
        $this->configResolverMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue(true));

        $this->fallbackConfigurationMock
            ->expects($this->never())
            ->method('hasParameter');

        self::assertTrue($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameterWithNoParameter()
    {
        $this->configResolverMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue(false));

        $this->fallbackConfigurationMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'))
            ->will($this->returnValue(true));

        self::assertTrue($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameterWithNoFallbackParameter()
    {
        $this->configResolverMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue(false));

        $this->fallbackConfigurationMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'))
            ->will($this->returnValue(false));

        self::assertFalse($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::getParameter
     */
    public function testGetParameter()
    {
        $this->configResolverMock
            ->expects($this->any())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue(true));

        $this->configResolverMock
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue('some_param_value'));

        $this->fallbackConfigurationMock
            ->expects($this->never())
            ->method('getParameter');

        self::assertEquals('some_param_value', $this->configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::getParameter
     */
    public function testGetFallbackParameter()
    {
        $this->configResolverMock
            ->expects($this->any())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue(false));

        $this->fallbackConfigurationMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'))
            ->will($this->returnValue(true));

        $this->configResolverMock
            ->expects($this->never())
            ->method('getParameter');

        $this->fallbackConfigurationMock
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->equalTo('some_param'))
            ->will($this->returnValue('some_param_value'));

        self::assertEquals('some_param_value', $this->configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\BlockManager\Ez\Configuration\ConfigResolverConfiguration::getParameter
     * @expectedException \OutOfBoundsException
     */
    public function testGetParameterThrowsOutOfBoundsException()
    {
        $this->configResolverMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue(false));

        $this->configuration->getParameter('some_param');
    }
}
