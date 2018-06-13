<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle;

use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\EzPublishBlockManagerBundle\Security\PolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetgenEzPublishBlockManagerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        /** @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension $blockManagerExtension */
        $blockManagerExtension = $container->getExtension('netgen_block_manager');
        $blockManagerExtension->addPlugin(new ExtensionPlugin($container));

        $container->addCompilerPass(new CompilerPass\View\DefaultViewTemplatesPass());
        $container->addCompilerPass(new CompilerPass\DefaultAppPreviewPass());
        $container->addCompilerPass(new CompilerPass\HttpCache\ConfigureLegacyHttpCachePass());
        $container->addCompilerPass(new CompilerPass\HttpCache\ConfigureHttpCachePass());

        /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension $ezCoreExtension */
        $ezCoreExtension = $container->getExtension('ezpublish');

        // @deprecated Check for existence of method for compatibility with eZ Publish 5.4.x
        // @todo Remove the check when support for 5.4.x ends
        if (method_exists($ezCoreExtension, 'addPolicyProvider')) {
            $ezCoreExtension->addPolicyProvider(new PolicyProvider());
        }
    }
}
