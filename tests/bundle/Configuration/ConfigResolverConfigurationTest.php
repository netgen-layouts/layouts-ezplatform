<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Configuration;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException;
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
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_block_manager'))
            ->will(self::returnValue(true));

        $this->fallbackConfigurationMock
            ->expects(self::never())
            ->method('hasParameter');

        self::assertTrue($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameterWithNoParameter(): void
    {
        $this->configResolverMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_block_manager'))
            ->will(self::returnValue(false));

        $this->fallbackConfigurationMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'))
            ->will(self::returnValue(true));

        self::assertTrue($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameterWithNoFallbackParameter(): void
    {
        $this->configResolverMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_block_manager'))
            ->will(self::returnValue(false));

        $this->fallbackConfigurationMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'))
            ->will(self::returnValue(false));

        self::assertFalse($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::getParameter
     */
    public function testGetParameter(): void
    {
        $this->configResolverMock
            ->expects(self::any())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_block_manager'))
            ->will(self::returnValue(true));

        $this->configResolverMock
            ->expects(self::once())
            ->method('getParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_block_manager'))
            ->will(self::returnValue('some_param_value'));

        $this->fallbackConfigurationMock
            ->expects(self::never())
            ->method('getParameter');

        self::assertSame('some_param_value', $this->configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::getParameter
     */
    public function testGetFallbackParameter(): void
    {
        $this->configResolverMock
            ->expects(self::any())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_block_manager'))
            ->will(self::returnValue(false));

        $this->fallbackConfigurationMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'))
            ->will(self::returnValue(true));

        $this->configResolverMock
            ->expects(self::never())
            ->method('getParameter');

        $this->fallbackConfigurationMock
            ->expects(self::once())
            ->method('getParameter')
            ->with(self::identicalTo('some_param'))
            ->will(self::returnValue('some_param_value'));

        self::assertSame('some_param_value', $this->configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Configuration\ConfigResolverConfiguration::getParameter
     */
    public function testGetParameterThrowsConfigurationException(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Parameter "some_param" does not exist in configuration.');

        $this->configResolverMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_block_manager'))
            ->will(self::returnValue(false));

        $this->fallbackConfigurationMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'))
            ->will(self::returnValue(false));

        $this->configuration->getParameter('some_param');
    }
}
