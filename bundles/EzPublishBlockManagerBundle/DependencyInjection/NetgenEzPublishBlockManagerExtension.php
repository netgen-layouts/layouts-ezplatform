<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

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
        // With this, we aim to load regular Block manager configuration parameters
        // and make them a default option for eZ Publish configuration which goes through
        // config resolver. It saves us from having to redefine entire default config in this
        // bundle again in format config resolver accepts.
        $blockManagerConfig = array('system' => array('default' => array()));
        $availableParameters = $container->getParameter('netgen_block_manager.available_configurations');
        foreach ($availableParameters as $parameterName) {
            $parameterValue = $container->getParameter('netgen_block_manager.' . $parameterName);
            $blockManagerConfig['system']['default'][$parameterName] = $parameterValue;
        }

        $configs = array_merge(array($blockManagerConfig), $configs);

        $extensionAlias = $this->getAlias();
        $configuration = new Configuration($extensionAlias);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('default_settings.yml');
        $loader->load('normalizers.yml');

        $loader->load('view/template_resolvers.yml');

        $processor = new ConfigurationProcessor($container, $extensionAlias);
        foreach ($blockManagerConfig['system']['default'] as $key => $value) {
            if (is_array($blockManagerConfig['system']['default'][$key])) {
                $processor->mapConfigArray($key, $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL);
            }
            else {
                $processor->mapSetting($key, $config);
            }
        }
    }
}
