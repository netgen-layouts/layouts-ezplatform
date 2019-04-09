<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle;

use Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass;
use Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\LayoutsEzPlatformBundle\Security\PolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetgenLayoutsEzPlatformBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension $layoutsExtension */
        $layoutsExtension = $container->getExtension('netgen_block_manager');
        $layoutsExtension->addPlugin(new ExtensionPlugin($container, $layoutsExtension));

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
