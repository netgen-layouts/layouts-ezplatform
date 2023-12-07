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
    ) {}

    public function generateLayoutUrl(UuidInterface $layoutId, array $parameters = []): string
    {
        $adminSiteAccess = $this->siteAccessGroups['admin_group'][0] ?? 'admin';

        return $this->innerGenerator->generateLayoutUrl($layoutId, ['siteaccess' => $adminSiteAccess]);
    }
}
