<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class NetgenEzPublishBlockManagerExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('normalizers.yml');
        $loader->load('view/template_resolvers.yml');
    }

    /**
     * Returns the config preprocessor closure.
     *
     * @return \Closure
     */
    public function getPreProcessor()
    {
        return function ($configs, ContainerBuilder $container) {
            $newConfigs = $configs;
            $appendConfigs = array();
            foreach ($configs as $key => $config) {
                if (isset($config['system'])) {
                    $appendConfigs[] = array('system' => $config['system']);
                    unset($config['system']);
                    $newConfigs[$key] = $config;
                }

                $newConfigs[] = array('system' => array('default' => $config));
            }

            return array_merge($newConfigs, $appendConfigs);
        };
    }

    /**
     * Returns the config postprocessor closure.
     *
     * @return \Closure
     */
    public function getPostProcessor()
    {
        return function ($config, ContainerBuilder $container) {
            $config['pagelayout'] = 'NetgenEzPublishBlockManagerBundle::pagelayout_resolver.html.twig';

            $processor = new ConfigurationProcessor($container, 'netgen_block_manager');
            foreach ($config as $key => $value) {
                if ($key !== 'system') {
                    if (is_array($config[$key])) {
                        $processor->mapConfigArray($key, $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL);
                    } else {
                        $processor->mapSetting($key, $config);
                    }
                }
            }

            unset($config['system']);

            return $config;
        };
    }
}
