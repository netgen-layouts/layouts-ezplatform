<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Configuration;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

class ConfigResolverConfigurationTest extends \PHPUnit_Framework_TestCase
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
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration
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

        $this->configResolverMock = $this->getMock(ConfigResolverInterface::class);
        $this->fallbackConfigurationMock = $this->getMock(ConfigurationInterface::class);

        $this->configuration = new ConfigResolverConfiguration($this->fallbackConfigurationMock);
        $this->configuration->setConfigResolver($this->configResolverMock);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::hasParameter
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::hasParameter
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::hasParameter
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::getParameter
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::getParameter
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::getParameter
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
