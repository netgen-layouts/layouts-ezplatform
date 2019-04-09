<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\ContentProvider;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\HttpFoundation\Request;

interface ContentExtractorInterface
{
    /**
     * Extracts the eZ Platform content object from provided request.
     */
    public function extractContent(Request $request): ?Content;

    /**
     * Extracts the eZ Platform location object from provided request.
     */
    public function extractLocation(Request $request): ?Location;
}
