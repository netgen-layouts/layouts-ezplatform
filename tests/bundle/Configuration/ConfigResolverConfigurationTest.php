<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Tests\Configuration;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\LayoutsBundle\Exception\ConfigurationException;
use Netgen\Bundle\LayoutsEzPlatformBundle\Configuration\ConfigResolverConfiguration;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ConfigResolverConfigurationTest extends TestCase
{
    private MockObject $configResolverMock;

    private MockObject $fallbackConfigurationMock;

    private ConfigResolverConfiguration $configuration;

    protected function setUp(): void
    {
        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $this->fallbackConfigurationMock = $this->createMock(ConfigurationInterface::class);

        $this->configuration = new ConfigResolverConfiguration(
            $this->configResolverMock,
            $this->fallbackConfigurationMock,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Configuration\ConfigResolverConfiguration::__construct
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameter(): void
    {
        $this->configResolverMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_layouts'))
            ->willReturn(true);

        $this->fallbackConfigurationMock
            ->expects(self::never())
            ->method('hasParameter');

        self::assertTrue($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameterWithNoParameter(): void
    {
        $this->configResolverMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_layouts'))
            ->willReturn(false);

        $this->fallbackConfigurationMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'))
            ->willReturn(true);

        self::assertTrue($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Configuration\ConfigResolverConfiguration::hasParameter
     */
    public function testHasParameterWithNoFallbackParameter(): void
    {
        $this->configResolverMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_layouts'))
            ->willReturn(false);

        $this->fallbackConfigurationMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'))
            ->willReturn(false);

        self::assertFalse($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Configuration\ConfigResolverConfiguration::getParameter
     */
    public function testGetParameter(): void
    {
        $this->configResolverMock
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_layouts'))
            ->willReturn(true);

        $this->configResolverMock
            ->expects(self::once())
            ->method('getParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_layouts'))
            ->willReturn('some_param_value');

        $this->fallbackConfigurationMock
            ->expects(self::never())
            ->method('getParameter');

        self::assertSame('some_param_value', $this->configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Configuration\ConfigResolverConfiguration::getParameter
     */
    public function testGetFallbackParameter(): void
    {
        $this->configResolverMock
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_layouts'))
            ->willReturn(false);

        $this->fallbackConfigurationMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'))
            ->willReturn(true);

        $this->configResolverMock
            ->expects(self::never())
            ->method('getParameter');

        $this->fallbackConfigurationMock
            ->expects(self::once())
            ->method('getParameter')
            ->with(self::identicalTo('some_param'))
            ->willReturn('some_param_value');

        self::assertSame('some_param_value', $this->configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Configuration\ConfigResolverConfiguration::getParameter
     */
    public function testGetParameterThrowsConfigurationException(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Parameter "some_param" does not exist in configuration.');

        $this->configResolverMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'), self::identicalTo('netgen_layouts'))
            ->willReturn(false);

        $this->fallbackConfigurationMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('some_param'))
            ->willReturn(false);

        $this->configuration->getParameter('some_param');
    }
}
