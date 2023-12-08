<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Debug\DataCollector;

use Netgen\Bundle\LayoutsDebugBundle\DataCollector\LayoutUrlGeneratorInterface;
use Ramsey\Uuid\UuidInterface;

final class LayoutUrlGenerator implements LayoutUrlGeneratorInterface
{
    private LayoutUrlGeneratorInterface $innerGenerator;

    /**
     * @var array<string, string[]>
     */
    private array $siteAccessGroups;

    private string $siteAccessGroupName;

    private string $defaultSiteAccessName;

    /**
     * @param array<string, string[]> $siteAccessGroups
     */
    public function __construct(
        LayoutUrlGeneratorInterface $innerGenerator,
        array $siteAccessGroups,
        string $siteAccessGroupName,
        string $defaultSiteAccessName
    ) {
        $this->innerGenerator = $innerGenerator;
        $this->siteAccessGroups = $siteAccessGroups;
        $this->siteAccessGroupName = $siteAccessGroupName;
        $this->defaultSiteAccessName = $defaultSiteAccessName;
    }

    public function generateLayoutUrl(UuidInterface $layoutId, array $parameters = []): string
    {
        $adminSiteAccess = $this->siteAccessGroups[$this->siteAccessGroupName][0] ?? $this->defaultSiteAccessName;

        return $this->innerGenerator->generateLayoutUrl($layoutId, ['siteaccess' => $adminSiteAccess]);
    }
}
