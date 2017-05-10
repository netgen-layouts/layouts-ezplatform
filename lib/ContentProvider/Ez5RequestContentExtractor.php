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
    protected $contentService;

    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    protected $locationService;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(ContentService $contentService, LocationService $locationService)
    {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
    }

    /**
     * Extracts the eZ Publish content value object from the provided request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content|void
     */
    public function extractContent(Request $request)
    {
        $content = $request->attributes->get('content');
        if ($content !== null && !$content instanceof Content) {
            return;
        }

        if ($content instanceof Content) {
            return $content;
        }

        $contentId = $request->attributes->get('contentId');
        $currentRoute = $request->attributes->get('_route');
        if ($contentId === null || $currentRoute !== UrlAliasRouter::URL_ALIAS_ROUTE_NAME) {
            return;
        }

        try {
            return $this->contentService->loadContent((int) $contentId);
        } catch (NotFoundException $e) {
            // Do nothing
        }
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
        $location = $request->attributes->get('location');
        if ($location !== null && !$location instanceof Location) {
            return;
        }

        if ($location instanceof Location) {
            return $location;
        }

        $locationId = $request->attributes->get('locationId');
        $currentRoute = $request->attributes->get('_route');
        if ($locationId === null || $currentRoute !== UrlAliasRouter::URL_ALIAS_ROUTE_NAME) {
            return;
        }

        try {
            return $this->locationService->loadLocation((int) $locationId);
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
