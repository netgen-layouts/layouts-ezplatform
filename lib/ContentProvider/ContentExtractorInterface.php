<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\ContentProvider;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\HttpFoundation\Request;

interface ContentExtractorInterface
{
    /**
     * Extracts the eZ Publish content object from provided request.
     */
    public function extractContent(Request $request): ?Content;

    /**
     * Extracts the eZ Publish location object from provided request.
     */
    public function extractLocation(Request $request): ?Location;
}
