<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Tests\DependencyInjection\CompilerPass\HttpCache;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\HttpCache\VarnishClientPass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class VarnishClientPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new VarnishClientPass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\HttpCache\VarnishClientPass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_layouts.http_cache.client.varnish', new Definition(null, [null, null]));
        $this->setDefinition('netgen_layouts.ezplatform.http_cache.varnish.host_header_provider', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.http_cache.client.varnish',
            1,
            new Reference('netgen_layouts.ezplatform.http_cache.varnish.host_header_provider'),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\HttpCache\VarnishClientPass::process
     */
    public function testProcessWithNoVarnishClient(): void
    {
        $this->setDefinition('netgen_layouts.ezplatform.http_cache.varnish.host_header_provider', new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasService('netgen_layouts.http_cache.client.varnish');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\HttpCache\VarnishClientPass::process
     */
    public function testProcessWithNoHostHeaderProvider(): void
    {
        $this->setDefinition('netgen_layouts.http_cache.client.varnish', new Definition(null, [null, null]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.http_cache.client.varnish',
            1,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\HttpCache\VarnishClientPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
