<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\HttpCache;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class VarnishClientPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (
            !$container->has('netgen_layouts.http_cache.client.varnish')
            || !$container->has('netgen_layouts.ezplatform.http_cache.varnish.host_header_provider')
        ) {
            return;
        }

        $varnishClient = $container->findDefinition('netgen_layouts.http_cache.client.varnish');

        $varnishClient->replaceArgument(
            1,
            new Reference('netgen_layouts.ezplatform.http_cache.varnish.host_header_provider'),
        );
    }
}
