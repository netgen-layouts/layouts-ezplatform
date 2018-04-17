<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignNode;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ViewNode;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin as BaseExtensionPlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ExtensionPlugin extends BaseExtensionPlugin
{
    /**
     * @var array
     */
    private static $siteAccessAwareSettings = [
        'view',
        'design',
    ];

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * Pre-processes the configuration before it is resolved.
     *
     * The point of the preprocessor is to generate eZ Publish siteaccess aware
     * configuration for every key that is available in self::$siteAccessAwareSettings.
     *
     * With this, the following:
     *
     * array(
     *     0 => array(
     *         'netgen_block_manager' => array(
     *             'view' => ...
     *         )
     *     )
     * )
     *
     * becomes:
     *
     * array(
     *     0 => array(
     *         'netgen_block_manager' => array(
     *             'view' => ...,
     *             'system' => array(
     *                 'default' => array(
     *                     'view' => ...
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * If the original array already has a system key, it will be removed and prepended
     * to configs generated from the original parameters.
     *
     * @param array $configs
     *
     * @return array
     */
    public function preProcessConfiguration(array $configs)
    {
        $newConfigs = $configs;
        $prependConfigs = [];
        foreach ($configs as $index => $config) {
            if (isset($config['system'])) {
                $prependConfigs[] = ['system' => $config['system']];
                unset($config['system']);
                $newConfigs[$index] = $config;
            }

            foreach ($config as $configName => $configValues) {
                if (!in_array($configName, self::$siteAccessAwareSettings, true)) {
                    unset($config[$configName]);
                }
            }

            $newConfigs[] = ['system' => ['default' => $config]];
        }

        return array_merge($prependConfigs, $newConfigs);
    }

    /**
     * Processes the configuration for the bundle.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode)
    {
        $configuration = new Configuration();
        $systemNode = $configuration->generateScopeBaseNode($rootNode);

        $nodes = [
            new ViewNode(),
            new DesignNode(),
        ];

        foreach ($nodes as $node) {
            $systemNode->append($node->getConfigurationNode());
        }
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
            if ($key === 'system' || !in_array($key, self::$siteAccessAwareSettings, true)) {
                continue;
            }

            is_array($config[$key]) ?
                $processor->mapConfigArray($key, $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL) :
                $processor->mapSetting($key, $config);
        }

        $designList = array_keys($config['design_list']);
        foreach ($config['system'] as $scope => $scopeConfig) {
            $this->validateCurrentDesign($scopeConfig['design'], $designList);
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
        return [
            __DIR__ . '/../Resources/config/block_type_groups.yml',
        ];
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
    private function fixUpViewConfig(array $config)
    {
        foreach ($config['system'] as $scope => $scopeConfig) {
            if ($scope === 'default') {
                continue;
            }

            foreach ($scopeConfig['view'] as $viewName => $viewConfig) {
                if (isset($config['system']['default']['view'][$viewName])) {
                    foreach ($config['system']['default']['view'][$viewName] as $context => $defaultRules) {
                        if (!isset($config['system'][$scope]['view'][$viewName][$context])) {
                            $config['system'][$scope]['view'][$viewName][$context] = [];
                        }

                        $config['system'][$scope]['view'][$viewName][$context] += $defaultRules;
                    }
                }
            }
        }

        return $config;
    }

    /**
     * Validates that the design specified in configuration exists in the system.
     *
     * @param string $currentDesign
     * @param array $designList
     */
    private function validateCurrentDesign($currentDesign, array $designList)
    {
        if ($currentDesign !== 'standard' && !in_array($currentDesign, $designList, true)) {
            throw new InvalidConfigurationException(
                sprintf(
                    'Design "%s" does not exist. Available designs are: %s',
                    $currentDesign,
                    implode(', ', $designList)
                )
            );
        }
    }
}
