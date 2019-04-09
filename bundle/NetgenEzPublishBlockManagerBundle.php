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
    public function build(ContainerBuilder $container): void
    {
        /** @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension $blockManagerExtension */
        $blockManagerExtension = $container->getExtension('netgen_block_manager');
        $blockManagerExtension->addPlugin(new ExtensionPlugin($container, $blockManagerExtension));

        $container->addCompilerPass(new CompilerPass\View\DefaultViewTemplatesPass());
        $container->addCompilerPass(new CompilerPass\DefaultAppPreviewPass());
        $container->addCompilerPass(new CompilerPass\HttpCache\ConfigureHttpCachePass());

        if (!interface_exists('Netgen\Layouts\Enterprise\API\Service\RoleService')) {
            /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension $ezCoreExtension */
            $ezCoreExtension = $container->getExtension('ezpublish');
            $ezCoreExtension->addPolicyProvider(new PolicyProvider());
        }
    }
}
