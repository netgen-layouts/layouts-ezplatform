<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\HttpCache;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\LocalPurgeClient;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\VarnishPurgeClient;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class ConfigureHttpCachePass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_block_manager.http_cache.client';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME) || !$container->has('ezplatform.http_cache.purge_client')) {
            return;
        }

        $purgeClient = $container->findDefinition('ezplatform.http_cache.purge_client');
        $purgeClientClass = $container->getParameterBag()->resolveValue($purgeClient->getClass());

        if (
            !is_a($purgeClientClass, VarnishPurgeClient::class, true)
            && !is_a($purgeClientClass, LocalPurgeClient::class, true)
        ) {
            $this->log(
                $container,
                sprintf(
                    'Cache clearing in Netgen Layouts cannot be automatically configured since eZ Platform purge client is neither an instance of "%s" nor "%s". Use Netgen Layouts "%s" config to enable or disable HTTP cache clearing.',
                    VarnishPurgeClient::class,
                    LocalPurgeClient::class,
                    'http_cache.invalidation.enabled'
                )
            );

            return;
        }

        if (!is_a($purgeClientClass, VarnishPurgeClient::class, true)) {
            $container->setAlias(
                self::SERVICE_NAME,
                'netgen_block_manager.http_cache.client.null'
            );
        }
    }

    /**
     * @deprecated
     *
     * Logs a message into the log. Acts as a BC layer to support Symfony 2.8.
     */
    private function log(ContainerBuilder $container, string $message): void
    {
        if (Kernel::VERSION_ID < 30300) {
            $compiler = $container->getCompiler();
            $compiler->addLogMessage($compiler->getLoggingFormatter()->format($this, $message));

            return;
        }

        $container->log($this, $message);
    }
}
