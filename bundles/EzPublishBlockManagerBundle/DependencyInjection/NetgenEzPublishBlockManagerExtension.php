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
        // With this, we aim to load regular Block manager configuration parameters for views
        // and make it a default option for eZ Publish configuration which goes through
        // config resolver. It saves us from having to redefine entire *_view rules in this
        // bundle again in format config resolver accepts.
        $blockManagerConfig = array('system' => array('default' => array()));
        foreach (array('block_view', 'layout_view') as $templateResolverItem) {
            if ($container->hasParameter('netgen_block_manager.' . $templateResolverItem)) {
                $templateResolverConfig = $container->getParameter('netgen_block_manager.' . $templateResolverItem);
                $blockManagerConfig['system']['default'][$templateResolverItem] = $templateResolverConfig;
            }
        }

        $configs = array_merge(array($blockManagerConfig), $configs);

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('default_settings.yml');
        $loader->load('normalizers.yml');

        $loader->load('view/template_resolvers.yml');

        $processor = new ConfigurationProcessor($container, 'netgen_ez_publish_block_manager');
        $processor->mapConfigArray('block_view', $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL);
        $processor->mapConfigArray('layout_view', $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL);
    }
}
