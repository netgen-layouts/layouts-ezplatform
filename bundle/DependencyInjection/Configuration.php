<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Netgen\Layouts\Utils\BackwardsCompatibility\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder as BaseTreeBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

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
