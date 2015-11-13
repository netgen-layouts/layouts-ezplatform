<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration as BlockManagerConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends SiteAccessConfiguration
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * Constructor.
     *
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root($this->alias);
        $systemNode = $this->generateScopeBaseNode($rootNode);

        $blockManagerConfiguration = new BlockManagerConfiguration(null);
        foreach ($blockManagerConfiguration->getAvailableNodeDefinitions() as $nodeDefinition) {
            $systemNode->append($nodeDefinition);
        }

        return $treeBuilder;
    }
}
