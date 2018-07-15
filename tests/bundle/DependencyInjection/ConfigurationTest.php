<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection;

use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class ConfigurationTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration
     */
    private $configuration;

    public function setUp(): void
    {
        $extensionMock = $this->createMock(ExtensionInterface::class);
        $extensionMock
            ->expects($this->any())
            ->method('getAlias')
            ->willReturn('alias');

        $this->configuration = new Configuration($extensionMock);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testGetConfigTreeBuilder(): void
    {
        $this->assertInstanceOf(TreeBuilder::class, $this->configuration->getConfigTreeBuilder());
    }
}
