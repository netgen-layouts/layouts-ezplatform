<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin as BaseExtensionPlugin;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionPlugin extends BaseExtensionPlugin
{
    /**
     * @var array
     */
    const SITEACCESS_AWARE_SETTINGS = array(
        'view',
    );

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * Pre-processes the configuration before it is resolved.
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
     * @param array $configs
     *
     * @return array
     */
    public function preProcessConfiguration(array $configs)
    {
        $newConfigs = $configs;
        $appendConfigs = array();
        foreach ($configs as $index => $config) {
            if (isset($config['system'])) {
                $appendConfigs[] = array('system' => $config['system']);
                unset($config['system']);
                $newConfigs[$index] = $config;
            }

            foreach ($config as $configName => $configValues) {
                if (!in_array($configName, self::SITEACCESS_AWARE_SETTINGS, true)) {
                    unset($config[$configName]);
                }
            }

            $newConfigs[] = array('system' => array('default' => $config));
        }

        return array_merge($newConfigs, $appendConfigs);
    }

    /**
     * Processes the configuration for the bundle.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function addConfiguration(NodeDefinition $rootNode)
    {
        $configuration = new Configuration();
        $systemNode = $configuration->generateScopeBaseNode($rootNode);

        $viewNode = new ViewNode();
        $systemNode->append($viewNode->getConfigurationNode());
    }

    /**
     * Post-processes the resolved configuration.
     *
     * The postprocessor calls eZ Publish mapConfigArray and mapSettings methods from siteaccess aware
     * configuration processor as per documentation, to make the configuration correctly apply to all
     * siteaccesses.
     *
     * @param array $config
     *
     * @return array
     */
    public function postProcessConfiguration(array $config)
    {
        $config = $this->fixUpViewConfig($config);

        $processor = new ConfigurationProcessor($this->container, 'netgen_block_manager');
        foreach ($config as $key => $value) {
            if ($key === 'system' || !in_array($key, self::SITEACCESS_AWARE_SETTINGS, true)) {
                continue;
            }

            if (is_array($config[$key])) {
                $processor->mapConfigArray($key, $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL);
            } else {
                $processor->mapSetting($key, $config);
            }
        }

        return $config;
    }

    /**
     * Returns the array of files to be appended to main bundle configuration.
     *
     * @return array
     */
    public function appendConfigurationFiles()
    {
        return array(
            __DIR__ . '/../Resources/config/block_type_groups.yml',
        );
    }

    /**
     * Ugly hack to support semantic view config. The problem is, eZ semantic config
     * supports only merging arrays up to second level, but in view config we have three.
     *
     * view:
     *     block_view:
     *         context:
     *             config1: ...
     *             config2: ...
     *
     * So instead of merging view.block_view.context, eZ merges view.block_view, thus loosing
     * a good deal of config.
     *
     * This iterates over all default view configs for each view and context, and merges
     * them in any found siteaccess or siteaccess group config, to make sure they're not lost
     * after contextualizer does it's thing.
     *
     * @param array $config
     *
     * @return array
     */
    protected function fixUpViewConfig(array $config)
    {
        foreach ($config['system'] as $scope => $scopeConfig) {
            if ($scope === 'default') {
                continue;
            }

            foreach ($scopeConfig['view'] as $viewName => $viewConfig) {
                if (isset($config['system']['default']['view'][$viewName])) {
                    foreach ($config['system']['default']['view'][$viewName] as $context => $defaultRules) {
                        if (!isset($config['system'][$scope]['view'][$viewName][$context])) {
                            $config['system'][$scope]['view'][$viewName][$context] = array();
                        }

                        $config['system'][$scope]['view'][$viewName][$context] = array_merge(
                            $defaultRules,
                            $config['system'][$scope]['view'][$viewName][$context]
                        );
                    }
                }
            }
        }

        return $config;
    }
}
