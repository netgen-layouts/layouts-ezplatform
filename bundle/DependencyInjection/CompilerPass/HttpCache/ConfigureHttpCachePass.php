<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection\CompilerPass\HttpCache;

use Ibexa\HttpCache\PurgeClient\LocalPurgeClient;
use Ibexa\HttpCache\PurgeClient\VarnishPurgeClient;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function is_a;
use function sprintf;

final class ConfigureHttpCachePass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_layouts.http_cache.client';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME) || !$container->has('ibexa.http_cache.purge_client_internal')) {
            return;
        }

        $purgeClient = $container->findDefinition('ibexa.http_cache.purge_client_internal');
        $purgeClientClass = $container->getParameterBag()->resolveValue($purgeClient->getClass());

        if (
            !is_a($purgeClientClass, VarnishPurgeClient::class, true)
            && !is_a($purgeClientClass, LocalPurgeClient::class, true)
        ) {
            $container->log(
                $this,
                sprintf(
                    'Cache clearing in Netgen Layouts cannot be automatically configured since Ibexa CMS purge client is neither an instance of "%s" nor "%s". Use Netgen Layouts "%s" config to enable or disable HTTP cache clearing.',
                    VarnishPurgeClient::class,
                    LocalPurgeClient::class,
                    'http_cache.invalidation.enabled',
                ),
            );

            return;
        }

        if (!is_a($purgeClientClass, VarnishPurgeClient::class, true)) {
            $container->setAlias(
                self::SERVICE_NAME,
                'netgen_layouts.ibexa.http_cache.client.local',
            );
        }
    }
}
