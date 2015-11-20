<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration as BlockManagerConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

abstract class Configuration extends SiteAccessConfiguration
{
    /**
     * Returns the config tree builder closure.
     *
     * @return \Closure
     */
    public function getConfigTreeBuilderClosure()
    {
        return function (ArrayNodeDefinition $rootNode, BlockManagerConfiguration $configuration) {
            $systemNode = $this->generateScopeBaseNode($rootNode);

            foreach ($configuration->getAvailableNodeDefinitions() as $nodeDefinition) {
                $systemNode->append($nodeDefinition);
            }
        };
    }
}
