<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\ContentProvider;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Symfony\Component\HttpFoundation\Request;

interface ContentExtractorInterface
{
    /**
     * Extracts the Ibexa CMS content object from provided request.
     */
    public function extractContent(Request $request): ?Content;

    /**
     * Extracts the Ibexa CMS location object from provided request.
     */
    public function extractLocation(Request $request): ?Location;
}
