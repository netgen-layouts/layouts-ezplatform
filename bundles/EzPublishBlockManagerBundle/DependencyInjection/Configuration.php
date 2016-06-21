<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration as BlockManagerConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class Configuration extends SiteAccessConfiguration
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
    }

    /**
     * Returns the config tree builder closure.
     *
     * @return \Closure
     */
    public function getConfigTreeBuilderClosure()
    {
        return function (ArrayNodeDefinition $rootNode, BlockManagerConfiguration $configuration) {
            $systemNode = $this->generateScopeBaseNode($rootNode);

            $systemNode->append($configuration->getTemplateResolverNodeDefinition('block_view'));
            $systemNode->append($configuration->getTemplateResolverNodeDefinition('item_view'));
            $systemNode->append($configuration->getTemplateResolverNodeDefinition('layout_view'));
            $systemNode->append($configuration->getTemplateResolverNodeDefinition('form_view'));
            $systemNode->append($configuration->getTemplateResolverNodeDefinition('rule_target_view'));
            $systemNode->append($configuration->getTemplateResolverNodeDefinition('rule_condition_view'));
        };
    }
}
