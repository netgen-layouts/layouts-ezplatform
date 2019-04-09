<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\ContentProvider;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class used to extract content and location from provided request in eZ Platform.
 */
final class RequestContentExtractor implements ContentExtractorInterface
{
    public function extractContent(Request $request): ?Content
    {
        $view = $request->attributes->get('view');
        if (!$view instanceof ContentValueView) {
            return null;
        }

        return $view->getContent();
    }

    public function extractLocation(Request $request): ?Location
    {
        $view = $request->attributes->get('view');
        if (!$view instanceof LocationValueView) {
            return null;
        }

        return $view->getLocation();
    }
}
