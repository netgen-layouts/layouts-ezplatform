<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\HttpCache;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\LocalPurgeClient;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\VarnishPurgeClient;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function is_a;
use function sprintf;

final class ConfigureHttpCachePass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_layouts.http_cache.client';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME) || !$container->has('ezplatform.http_cache.purge_client_internal')) {
            return;
        }

        $purgeClient = $container->findDefinition('ezplatform.http_cache.purge_client_internal');
        $purgeClientClass = $container->getParameterBag()->resolveValue($purgeClient->getClass());

        if (
            !is_a($purgeClientClass, VarnishPurgeClient::class, true)
            && !is_a($purgeClientClass, LocalPurgeClient::class, true)
        ) {
            $container->log(
                $this,
                sprintf(
                    'Cache clearing in Netgen Layouts cannot be automatically configured since eZ Platform purge client is neither an instance of "%s" nor "%s". Use Netgen Layouts "%s" config to enable or disable HTTP cache clearing.',
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
                'netgen_layouts.ezplatform.http_cache.client.local',
            );
        }
    }
}
