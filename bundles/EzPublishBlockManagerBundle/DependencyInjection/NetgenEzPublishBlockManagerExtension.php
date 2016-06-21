<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

class NetgenEzPublishBlockManagerExtension extends Extension implements PrependExtensionInterface
{
    protected $siteAccessAwareSettings = array(
        'block_view',
        'item_view',
        'layout_view',
        'form_view',
        'rule_target_view',
        'rule_condition_view',
    );

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

        $loader->load('default_settings.yml');
        $loader->load('services/configuration.yml');
        $loader->load('services/block_definitions.yml');
        $loader->load('services/event_listeners.yml');
        $loader->load('services/validators.yml');
        $loader->load('services/parameters.yml');
        $loader->load('services/templating.yml');
        $loader->load('services/items.yml');
        $loader->load('services/layout_resolver/condition_types.yml');
        $loader->load('services/layout_resolver/target_types.yml');
        $loader->load('services/layout_resolver/target_handlers.yml');
        $loader->load('services/collection/query_types.yml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $prependConfigs = array(
            'block_definitions.yml' => 'netgen_block_manager',
            'block_types.yml' => 'netgen_block_manager',
            'query_types.yml' => 'netgen_block_manager',
            'sources.yml' => 'netgen_block_manager',
            'view/block_view.yml' => 'netgen_block_manager',
        );

        foreach ($prependConfigs as $configFile => $prependConfig) {
            $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            $config = Yaml::parse(file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }
    }

    /**
     * Returns the config preprocessor closure.
     *
     * The point of the preprocessor is to generate eZ Publish siteaccess aware
     * configuration for every key that is available under 'netgen_block_manager' key.
     *
     * With this, the following:
     *
     * array(
     *     0 => array(
     *         'netgen_block_manager' => array(
     *             'param' => 'value'
     *         )
     *     )
     * )
     *
     * becomes:
     *
     * array(
     *     0 => array(
     *         'netgen_block_manager' => array(
     *             'param' => 'value',
     *             'system' => array(
     *                 'default' => array(
     *                     'param' => 'value'
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * If the original array already has a system key, it will be removed and appended
     * to configs generated from the original parameters.
     *
     * @return \Closure
     */
    public function getPreProcessor()
    {
        return function ($configs, ContainerBuilder $container) {

            $newConfigs = $configs;
            $appendConfigs = array();
            foreach ($configs as $index => $config) {
                if (isset($config['system'])) {
                    $appendConfigs[] = array('system' => $config['system']);
                    unset($config['system']);
                    $newConfigs[$index] = $config;
                }

                foreach ($config as $configName => $configValues) {
                    if (!in_array($configName, $this->siteAccessAwareSettings)) {
                        unset($config[$configName]);
                    }
                }

                $newConfigs[] = array('system' => array('default' => $config));
            }

            return array_merge($newConfigs, $appendConfigs);
        };
    }

    /**
     * Returns the config postprocessor closure.
     *
     * The postprocessor calls eZ Publish mapConfigArray and mapSettings methods from siteaccess aware
     * configuration processor as per documentation, to make the configuration correctly apply to all
     * siteaccesses.
     *
     * @return \Closure
     */
    public function getPostProcessor()
    {
        return function ($config, ContainerBuilder $container) {
            $processor = new ConfigurationProcessor($container, 'netgen_block_manager');
            foreach ($config as $key => $value) {
                if ($key === 'system' || !in_array($key, $this->siteAccessAwareSettings)) {
                    continue;
                }

                if (is_array($config[$key])) {
                    $processor->mapConfigArray($key, $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL);
                } else {
                    $processor->mapSetting($key, $config);
                }
            }

            return $config;
        };
    }
}
