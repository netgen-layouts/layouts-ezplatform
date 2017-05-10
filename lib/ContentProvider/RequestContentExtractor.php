<?php

namespace Netgen\BlockManager\Ez\ContentProvider;

use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use Symfony\Component\HttpFoundation\Request;

class RequestContentExtractor implements ContentExtractorInterface
{
    /**
     * Extracts the eZ Publish content value object from provided request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content|void
     */
    public function extractContent(Request $request)
    {
        $view = $request->attributes->get('view');
        if (!$view instanceof ContentValueView) {
            return;
        }

        return $view->getContent();
    }

    /**
     * Extracts the eZ Publish location value object from provided request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|void
     */
    public function extractLocation(Request $request)
    {
        $view = $request->attributes->get('view');
        if (!$view instanceof LocationValueView) {
            return;
        }

        return $view->getLocation();
    }
}
