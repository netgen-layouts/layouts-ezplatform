<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection;

use eZ\Publish\SPI\FieldType\Nameable;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

use function array_key_exists;
use function file_get_contents;
use function interface_exists;

final class NetgenLayoutsEzPlatformExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param mixed[] $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');

        $loader = new DelegatingLoader(
            new LoaderResolver(
                [
                    new GlobFileLoader($container, $locator),
                    new YamlFileLoader($container, $locator),
                ],
            ),
        );

        $loader->load('default_settings.yaml');
        $loader->load('services/**/*.yaml', 'glob');

        /** @var array<string, string> $activatedBundles */
        $activatedBundles = $container->getParameter('kernel.bundles');

        $container->setParameter(
            'netgen_layouts.ezplatform.is_enterprise',
            array_key_exists('NetgenLayoutsEnterpriseBundle', $activatedBundles),
        );

        if (!array_key_exists('NetgenLayoutsEnterpriseEzPlatformBundle', $activatedBundles)) {
            $loader->load('enterprise/services.yaml');
        }

        if (array_key_exists('EzPlatformAdminUiBundle', $activatedBundles)) {
            $loader->load('admin/services.yaml');
        }

        if (array_key_exists('NetgenTagsBundle', $activatedBundles)) {
            $loader->load('eztags/services.yaml');
        }

        if (array_key_exists('NetgenLayoutsDebugBundle', $activatedBundles)) {
            $loader->load('debug/services.yaml');
        }

        $loader->load(
            // Nameable interface for field types does not exist in eZ Platform v3
            interface_exists(Nameable::class) ?
                'ezplatform_v2/http_cache.yaml' :
                'ezplatform_v3/http_cache.yaml',
        );
    }

    public function prepend(ContainerBuilder $container): void
    {
        $prependConfigs = [
            'block_definitions.yaml' => 'netgen_layouts',
            'block_types.yaml' => 'netgen_layouts',
            'query_types.yaml' => 'netgen_layouts',
            'value_types.yaml' => 'netgen_layouts',
            'view/block_view.yaml' => 'netgen_layouts',
            'view/item_view.yaml' => 'netgen_layouts',
            'view/rule_condition_view.yaml' => 'netgen_layouts',
            'view/rule_target_view.yaml' => 'netgen_layouts',
            'view/rule_view.yaml' => 'netgen_layouts',
            'view/layout_view.yaml' => 'netgen_layouts',
            'ezplatform/image.yaml' => 'ezpublish',
            'framework/twig.yaml' => 'twig',
        ];

        foreach ($prependConfigs as $configFile => $prependConfig) {
            $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }
    }

    /**
     * @param mixed[] $config
     */
    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration($this);
    }
}
