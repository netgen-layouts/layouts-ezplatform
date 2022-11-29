<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle;

use Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass;
use Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\LayoutsEzPlatformBundle\Security\PolicyProvider;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use function interface_exists;

final class NetgenLayoutsEzPlatformBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension $layoutsExtension */
        $layoutsExtension = $container->getExtension('netgen_layouts');
        $layoutsExtension->addPlugin(new ExtensionPlugin($container, $layoutsExtension));

        $container->addCompilerPass(new CompilerPass\View\DefaultViewTemplatesPass());
        $container->addCompilerPass(new CompilerPass\ComponentPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1000);
        $container->addCompilerPass(new CompilerPass\DefaultAppPreviewPass());
        $container->addCompilerPass(new CompilerPass\HttpCache\ConfigureHttpCachePass());
        $container->addCompilerPass(new CompilerPass\HttpCache\VarnishClientPass());

        if (!interface_exists('Netgen\Layouts\Enterprise\API\Service\RoleService')) {
            /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension $ezCoreExtension */
            $ezCoreExtension = $container->getExtension('ezpublish');
            $ezCoreExtension->addPolicyProvider(new PolicyProvider());
        }
    }
}
