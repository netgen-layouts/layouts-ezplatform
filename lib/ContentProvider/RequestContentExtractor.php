<?php

namespace Netgen\BlockManager\Ez\ContentProvider;

use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use Symfony\Component\HttpFoundation\Request;

class RequestContentExtractor implements ContentExtractorInterface
{
    public function extractContent(Request $request)
    {
        $view = $request->attributes->get('view');
        if (!$view instanceof ContentValueView) {
            return;
        }

        return $view->getContent();
    }

    public function extractLocation(Request $request)
    {
        $view = $request->attributes->get('view');
        if (!$view instanceof LocationValueView) {
            return;
        }

        return $view->getLocation();
    }
}
