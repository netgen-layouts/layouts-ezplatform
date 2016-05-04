<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Configuration;

use Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

class ConfigResolverConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the tests.
     */
    protected function setUp()
    {
        if (!interface_exists('eZ\Publish\Core\MVC\ConfigResolverInterface')) {
            $this->markTestSkipped('No eZ Publish installed, ConfigResolverConfiguration tests skipped.');
        }
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameter()
    {
        $configResolver = $this->getMock(ConfigResolverInterface::class);
        $configResolver
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue(true));

        $configuration = new ConfigResolverConfiguration();
        $configuration->setConfigResolver($configResolver);
        self::assertTrue($configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameterWithNoParameter()
    {
        $configResolver = $this->getMock(ConfigResolverInterface::class);
        $configResolver
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue(false));

        $configuration = new ConfigResolverConfiguration();
        $configuration->setConfigResolver($configResolver);
        self::assertFalse($configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::getParameter
     */
    public function testGetParameter()
    {
        $configResolver = $this->getMock(ConfigResolverInterface::class);
        $configResolver
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue(true));
        $configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue('some_param_value'));

        $configuration = new ConfigResolverConfiguration();
        $configuration->setConfigResolver($configResolver);
        self::assertEquals('some_param_value', $configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::setConfigResolver
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::getParameter
     * @expectedException \InvalidArgumentException
     */
    public function testGetParameterThrowsInvalidArgumentException()
    {
        $configResolver = $this->getMock(ConfigResolverInterface::class);
        $configResolver
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('some_param'), $this->equalTo('netgen_block_manager'))
            ->will($this->returnValue(false));

        $configuration = new ConfigResolverConfiguration();
        $configuration->setConfigResolver($configResolver);
        $configuration->getParameter('some_param');
    }
}
