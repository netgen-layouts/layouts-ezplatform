<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration as BlockManagerConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends SiteAccessConfiguration
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $blockManagerConfiguration = new BlockManagerConfiguration();

        $rootNode = $treeBuilder->root('netgen_ez_publish_block_manager');
        $systemNode = $this->generateScopeBaseNode($rootNode);

        $blockManagerConfiguration->addTemplateResolverNode($systemNode, 'block_view');
        $blockManagerConfiguration->addTemplateResolverNode($systemNode, 'layout_view');

        return $treeBuilder;
    }
}
