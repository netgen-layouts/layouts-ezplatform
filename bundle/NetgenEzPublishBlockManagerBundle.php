<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle;

use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\EzPublishBlockManagerBundle\Security\PolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetgenEzPublishBlockManagerBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        /** @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension $blockManagerExtension */
        $blockManagerExtension = $container->getExtension('netgen_block_manager');
        $blockManagerExtension->addPlugin(new ExtensionPlugin($container));

        $container->addCompilerPass(new CompilerPass\View\DefaultViewTemplatesPass());
        $container->addCompilerPass(new CompilerPass\DefaultAppPreviewPass());
        $container->addCompilerPass(new CompilerPass\HttpCache\ConfigureHttpCachePass());

        /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension $ezCoreExtension */
        $ezCoreExtension = $container->getExtension('ezpublish');
        $ezCoreExtension->addPolicyProvider(new PolicyProvider());
    }
}
