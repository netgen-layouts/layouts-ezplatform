<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class Configuration extends SiteAccessConfiguration
{
    public function __construct(private ExtensionInterface $extension)
    {
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder($this->extension->getAlias());
    }
}
