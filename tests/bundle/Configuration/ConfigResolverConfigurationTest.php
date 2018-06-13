<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Configuration;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration;
use PHPUnit\Framework\TestCase;

final class ConfigResolverConfigurationTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $configResolverMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $fallbackConfigurationMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration
     */
    private $configuration;

    public function setUp(): void
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameter(): void
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameterWithNoParameter(): void
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameterWithNoFallbackParameter(): void
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::getParameter
     */
    public function testGetParameter(): void
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::getParameter
     */
    public function testGetFallbackParameter(): void
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::getParameter
     * @expectedException \Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException
     * @expectedExceptionMessage Parameter "some_param" does not exist in configuration.
     */
    public function testGetParameterThrowsConfigurationException(): void
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

        $this->configuration->getParameter('some_param');
    }
}
