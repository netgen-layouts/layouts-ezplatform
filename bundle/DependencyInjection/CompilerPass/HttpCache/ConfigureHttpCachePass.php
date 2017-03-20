<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\HttpCache;

use eZ\Publish\Core\MVC\Symfony\Cache\Http\FOSPurgeClient;
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

        if (!is_a($purgeClientClass, FOSPurgeClient::class, true)) {
            $container->setAlias(
                self::SERVICE_NAME,
                'netgen_block_manager.http_cache.client.null'
            );
        }
    }
}
