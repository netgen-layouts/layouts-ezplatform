<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DesignNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\ViewNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPlugin as BaseExtensionPlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class ExtensionPlugin extends BaseExtensionPlugin
{
    private const SITEACCCESS_AWARE_SETTINGS = [
        'view',
        'design',
    ];

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    /**
     * @var \Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    private $extension;

    public function __construct(ContainerBuilder $container, ExtensionInterface $extension)
    {
        $this->container = $container;
        $this->extension = $extension;
    }

    /**
     * Pre-processes the configuration before it is resolved.
     *
     * The point of the preprocessor is to generate eZ Platform siteaccess aware
     * configuration for every key that is available in self::SITEACCCESS_AWARE_SETTINGS.
     *
     * With this, the following:
     *
     * [
     *     0 => [
     *         'netgen_layouts' => [
     *             'view' => ...
     *         ]
     *     ]
     * ]
     *
     * becomes:
     *
     * [
     *     0 => [
     *         'netgen_layouts' => [
     *             'view' => ...,
     *             'system' => [
     *                 'default' => [
     *                     'view' => ...
     *                 ]
     *             ]
     *         ]
     *     ]
     * ]
     *
     * If the original array already has a system key, it will be removed and prepended
     * to configs generated from the original parameters.
     */
    public function preProcessConfiguration(array $configs): array
    {
        $newConfigs = $configs;
        $prependConfigs = [];
        foreach ($configs as $index => $config) {
            if (isset($config['system'])) {
                $prependConfigs[] = ['system' => $config['system']];
                unset($config['system']);
                $newConfigs[$index] = $config;
            }

            foreach (array_keys($config) as $configName) {
                if (!in_array($configName, self::SITEACCCESS_AWARE_SETTINGS, true)) {
                    unset($config[$configName]);
                }
            }

            $newConfigs[] = ['system' => ['default' => $config]];
        }

        return array_merge($prependConfigs, $newConfigs);
    }

    public function addConfiguration(ArrayNodeDefinition $rootNode): void
    {
        $configuration = new Configuration($this->extension);
        $systemNode = $configuration->generateScopeBaseNode($rootNode);

        foreach ($this->getConfigurationNodes() as $node) {
            $systemNode->append($node->getConfigurationNode());
        }
    }

    /**
     * Post-processes the resolved configuration.
     *
     * The postprocessor calls eZ Platform mapConfigArray and mapSettings methods from siteaccess aware
     * configuration processor as per documentation, to make the configuration correctly apply to all
     * siteaccesses.
     */
    public function postProcessConfiguration(array $config): array
    {
        $config = $this->fixUpViewConfig($config);

        $processor = new ConfigurationProcessor($this->container, $this->extension->getAlias());
        foreach (array_keys($config) as $key) {
            if ($key === 'system' || !in_array($key, self::SITEACCCESS_AWARE_SETTINGS, true)) {
                continue;
            }

            is_array($config[$key]) ?
                $processor->mapConfigArray($key, $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL) :
                $processor->mapSetting($key, $config);
        }

        $designList = array_keys($config['design_list']);
        foreach ($config['system'] as $scopeConfig) {
            $this->validateCurrentDesign($scopeConfig['design'], $designList);
        }

        return $config;
    }

    public function appendConfigurationFiles(): array
    {
        return [
            __DIR__ . '/../Resources/config/block_type_groups.yaml',
        ];
    }

    protected function getConfigurationNodes(): array
    {
        return [
            new ViewNode(),
            new DesignNode(),
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
     */
    private function fixUpViewConfig(array $config): array
    {
        foreach ($config['system'] as $scope => $scopeConfig) {
            if ($scope === 'default') {
                continue;
            }

            foreach (array_keys($scopeConfig['view']) as $viewName) {
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
     */
    private function validateCurrentDesign(string $currentDesign, array $designList): void
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
