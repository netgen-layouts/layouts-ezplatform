<?php

namespace Netgen\BlockManager\Ez\ContentProvider;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated Class used to extract content and location from provided request
 * in eZ Publish 5
 */
class Ez5RequestContentExtractor implements ContentExtractorInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    public function __construct(ContentService $contentService, LocationService $locationService)
    {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
    }

    public function extractContent(Request $request)
    {
        $content = $request->attributes->get('content');
        if ($content !== null && !$content instanceof Content) {
            return null;
        }

        if ($content instanceof Content) {
            return $content;
        }

        $contentId = $request->attributes->get('contentId');
        $currentRoute = $request->attributes->get('_route');
        if ($contentId === null || $currentRoute !== UrlAliasRouter::URL_ALIAS_ROUTE_NAME) {
            return null;
        }

        try {
            return $this->contentService->loadContent((int) $contentId);
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    public function extractLocation(Request $request)
    {
        $location = $request->attributes->get('location');
        if ($location !== null && !$location instanceof Location) {
            return null;
        }

        if ($location instanceof Location) {
            return $location;
        }

        $locationId = $request->attributes->get('locationId');
        $currentRoute = $request->attributes->get('_route');
        if ($locationId === null || $currentRoute !== UrlAliasRouter::URL_ALIAS_ROUTE_NAME) {
            return null;
        }

        try {
            return $this->locationService->loadLocation((int) $locationId);
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
