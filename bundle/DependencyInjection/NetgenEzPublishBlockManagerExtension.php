<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

final class NetgenEzPublishBlockManagerExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('default_settings.yml');
        $loader->load('services/configuration.yml');
        $loader->load('services/block_definitions.yml');
        $loader->load('services/validators.yml');
        $loader->load('services/parameters.yml');
        $loader->load('services/templating.yml');
        $loader->load('services/items.yml');
        $loader->load('services/forms.yml');
        $loader->load('services/services.yml');
        $loader->load('services/locale.yml');
        $loader->load('services/context.yml');
        $loader->load('services/layout_resolver/condition_types.yml');
        $loader->load('services/layout_resolver/target_types.yml');
        $loader->load('services/layout_resolver/target_handlers.yml');
        $loader->load('services/layout_resolver/forms.yml');
        $loader->load('services/collection/query_types.yml');

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        if (in_array('NetgenTagsBundle', $activatedBundles, true)) {
            $loader->load('eztags/services.yml');
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $prependConfigs = [
            'block_definitions.yml' => 'netgen_block_manager',
            'query_types.yml' => 'netgen_block_manager',
            'value_types.yml' => 'netgen_block_manager',
            'view/block_view.yml' => 'netgen_block_manager',
            'view/item_view.yml' => 'netgen_block_manager',
            'view/rule_condition_view.yml' => 'netgen_block_manager',
            'view/rule_target_view.yml' => 'netgen_block_manager',
            'ezplatform/image.yml' => 'ezpublish',
        ];

        foreach ($prependConfigs as $configFile => $prependConfig) {
            $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }

        // Register templates from the bundle under @NetgenEzPublishBlockManager namespace
        // to keep external references to templates working after they were
        // moved to theme based paths
        $container->prependExtensionConfig(
            'twig',
            [
                'paths' => [
                    __DIR__ . '/../Resources/views/ngbm/themes/standard' => 'NetgenEzPublishBlockManager',
                ],
            ]
        );
    }
}
