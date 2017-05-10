<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Configuration;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration;
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
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration
     */
    protected $configuration;

    /**
     * Sets up the tests.
     */
    protected function setUp()
    {
        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $this->fallbackConfigurationMock = $this->createMock(ConfigurationInterface::class);

        $this->configuration = new ConfigResolverConfiguration(
            $this->configResolverMock,
            $this->fallbackConfigurationMock
        );
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

        $this->assertTrue($this->configuration->hasParameter('some_param'));
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

        $this->assertTrue($this->configuration->hasParameter('some_param'));
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

        $this->assertFalse($this->configuration->hasParameter('some_param'));
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

        $this->assertEquals('some_param_value', $this->configuration->getParameter('some_param'));
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

        $this->assertEquals('some_param_value', $this->configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::getParameter
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Parameter "some_param" does not exist in configuration.
     */
    public function testGetParameterThrowsInvalidArgumentException()
    {
        $this->configResolverMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue(false));

        $this->configuration->getParameter('some_param');
    }
}
