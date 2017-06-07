<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\HttpCache;

use eZ\Publish\Core\MVC\Symfony\Cache\Http\FOSPurgeClient;
use eZ\Publish\Core\MVC\Symfony\Cache\Http\LocalPurgeClient;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigureHttpCachePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.http_cache.client';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $purgeClient = $container->findDefinition('ezpublish.http_cache.purge_client');

        $purgeClientClass = $purgeClient->getClass();
        if (strpos($purgeClientClass, '%') === 0) {
            $purgeClientClass = $container->getParameter(
                str_replace('%', '', $purgeClientClass)
            );
        }

        if (
            !is_a($purgeClientClass, FOSPurgeClient::class, true) &&
            !is_a($purgeClientClass, LocalPurgeClient::class, true)
        ) {
            $compiler = $container->getCompiler();
            $formatter = $compiler->getLoggingFormatter();

            $compiler->addLogMessage(
                $formatter->format(
                    $this,
                    sprintf(
                        'Cache clearing in Netgen Layouts cannot be automatically configured since eZ Publish purge client is neither an instance of "%s" nor "%s". Use Netgen Layouts "%s" config to enable or disable HTTP cache clearing.',
                        FOSPurgeClient::class,
                        LocalPurgeClient::class,
                        'http_cache.invalidation.enabled'
                    )
                )
            );

            return;
        }

        if (!is_a($purgeClientClass, FOSPurgeClient::class, true)) {
            $container->setAlias(
                self::SERVICE_NAME,
                'netgen_block_manager.http_cache.client.null'
            );
        }
    }
}
