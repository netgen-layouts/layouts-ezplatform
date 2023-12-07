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

    /**
     * @param array<string, string[]> $siteAccessGroups
     */
    public function __construct(
        LayoutUrlGeneratorInterface $innerGenerator,
        array $siteAccessGroups
    ) {
        $this->innerGenerator = $innerGenerator;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    public function generateLayoutUrl(UuidInterface $layoutId, array $parameters = []): string
    {
        $adminSiteAccess = $this->siteAccessGroups['admin_group'][0] ?? 'admin';

        return $this->innerGenerator->generateLayoutUrl($layoutId, ['siteaccess' => $adminSiteAccess]);
    }
}
