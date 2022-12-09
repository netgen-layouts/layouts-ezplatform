<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\ContentProvider;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\Core\MVC\Symfony\View\LocationValueView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class used to extract content and location from provided request in Ibexa CMS.
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
