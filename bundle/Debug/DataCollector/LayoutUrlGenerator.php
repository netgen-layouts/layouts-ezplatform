<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Debug\DataCollector;

use Netgen\Bundle\LayoutsDebugBundle\DataCollector\LayoutUrlGeneratorInterface;
use Ramsey\Uuid\UuidInterface;

final class LayoutUrlGenerator implements LayoutUrlGeneratorInterface
{
    /**
     * @param array<string, string[]> $siteAccessGroups
     */
    public function __construct(
        private LayoutUrlGeneratorInterface $innerGenerator,
        private array $siteAccessGroups,
        private string $siteAccessGroupName,
        private string $defaultSiteAccessName,
    ) {}

    public function generateLayoutUrl(UuidInterface $layoutId, array $parameters = []): string
    {
        $adminSiteAccess = $this->siteAccessGroups[$this->siteAccessGroupName][0] ?? $this->defaultSiteAccessName;

        return $this->innerGenerator->generateLayoutUrl($layoutId, ['siteaccess' => $adminSiteAccess]);
    }
}
