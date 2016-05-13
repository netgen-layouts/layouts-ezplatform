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
    protected $siteAccessAwareSettings = array(
        'block_view',
        'layout_view',
        'query_view',
        'pagelayout',
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
        $loader->load('services/layout_resolver/condition_matchers.yml');
        $loader->load('services/layout_resolver/target_builders.yml');
        $loader->load('services/layout_resolver/target_handlers.yml');
        $loader->load('services/collection/value_loaders.yml');
        $loader->load('services/collection/value_converters.yml');
        $loader->load('services/collection/query_types.yml');
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
     * The postprocessor does two things:
     *
     * 1) When we convert the configs to siteaccess aware configs with preprocessor,
     * pagelayout config is also converted. However, since here we need to override
     * the global netgen_block_manager.pagelayout, but NOT the converted one (otherwise
     * we would have an infinite loop when rendering the pagelayout), we need to specifically
     * set it here, after the semantic configuration is processed and merged.
     *
     * 2) It calls eZ Publish mapConfigArray and mapSettings methods from siteaccess aware
     * configuration processor as per documentation, to make the configuration correctly
     * apply to all siteaccesses.
     *
     * @return \Closure
     */
    public function getPostProcessor()
    {
        return function ($config, ContainerBuilder $container) {
            $config['pagelayout'] = 'NetgenEzPublishBlockManagerBundle::pagelayout_resolver.html.twig';

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
