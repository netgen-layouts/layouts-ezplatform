<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle;

use Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection\CompilerPass;
use Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\LayoutsIbexaBundle\Security\PolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use function interface_exists;

final class NetgenLayoutsIbexaBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension $layoutsExtension */
        $layoutsExtension = $container->getExtension('netgen_layouts');
        $layoutsExtension->addPlugin(new ExtensionPlugin($container, $layoutsExtension));

        $container->addCompilerPass(new CompilerPass\View\DefaultViewTemplatesPass());
        $container->addCompilerPass(new CompilerPass\DefaultAppPreviewPass());
        $container->addCompilerPass(new CompilerPass\HttpCache\ConfigureHttpCachePass());
        $container->addCompilerPass(new CompilerPass\HttpCache\VarnishClientPass());

        if (!interface_exists('Netgen\Layouts\Enterprise\API\Service\RoleService')) {
            /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $ibexaCoreExtension */
            $ibexaCoreExtension = $container->getExtension('ibexa');
            $ibexaCoreExtension->addPolicyProvider(new PolicyProvider());
        }
    }
}
