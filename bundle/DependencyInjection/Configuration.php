<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder as BaseTreeBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\TreeBuilder;

final class Configuration extends SiteAccessConfiguration
{
    /**
     * @var \Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    private $extension;

    public function __construct(ExtensionInterface $extension)
    {
        $this->extension = $extension;
    }

    public function getConfigTreeBuilder(): BaseTreeBuilder
    {
        return new TreeBuilder($this->extension->getAlias());
    }
}
