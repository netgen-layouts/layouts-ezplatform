<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

class NetgenEzPublishBlockManagerExtension extends Extension implements PrependExtensionInterface
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
        $extensionAlias = $this->getAlias();
        $blockManagerExtensionAlias = 'netgen_block_manager';

        // With this, we aim to load regular Block Manager configuration parameters
        // and make them a default option for eZ Publish configuration which goes through
        // config resolver. It saves us from having to redefine entire default config in this
        // bundle again in format config resolver accepts.
        $blockManagerConfig = array('system' => array('default' => array()));
        $availableParameters = $container->getParameter($blockManagerExtensionAlias . '.available_parameters');
        foreach ($availableParameters as $parameterName) {
            $parameterValue = $container->getParameter($blockManagerExtensionAlias . '.' . $parameterName);
            $blockManagerConfig['system']['default'][$parameterName] = $parameterValue;
        }

        $configs = array_merge(array($blockManagerConfig), $configs);

        $configuration = new Configuration($extensionAlias);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
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

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = array(
            'pagelayout' => 'NetgenEzPublishBlockManagerBundle::pagelayout_resolver.html.twig',
        );

        $container->prependExtensionConfig('netgen_block_manager', $config);
    }
}
