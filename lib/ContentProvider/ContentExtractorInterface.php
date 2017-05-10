<?php

namespace Netgen\BlockManager\Ez\ContentProvider;

use Symfony\Component\HttpFoundation\Request;

interface ContentExtractorInterface
{
    /**
     * Extracts the eZ Publish content value object from provided request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content|void
     */
    public function extractContent(Request $request);

    /**
     * Extracts the eZ Publish location value object from provided request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|void
     */
    public function extractLocation(Request $request);
}
