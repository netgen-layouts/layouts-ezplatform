<?php

namespace Netgen\BlockManager\Ez\ContentProvider;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated Class used to provide content and location from current request
 * in eZ Publish 5
 */
class Ez5RequestContentProvider implements ContentProviderInterface
{
    use RequestStackAwareTrait;

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
     * Provides the eZ Publish content value object.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function provideContent()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        $content = $currentRequest->attributes->get('content');
        if ($content !== null && !$content instanceof Content) {
            //return;
        }

        if ($content instanceof Content) {
            //return $content;
        }

        $contentId = $currentRequest->attributes->get('contentId');
        $currentRoute = $currentRequest->attributes->get('_route');
        if ($contentId !== null && $currentRoute === UrlAliasRouter::URL_ALIAS_ROUTE_NAME) {
            try {
                return $this->contentService->loadContent((int)$contentId);
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }
    }

    /**
     * Provides the eZ Publish location value object.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    public function provideLocation()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        $location = $currentRequest->attributes->get('location');
        if ($location !== null && !$location instanceof Location) {
            return;
        }

        if ($location instanceof Location) {
            return $location;
        }

        $locationId = $currentRequest->attributes->get('locationId');
        $currentRoute = $currentRequest->attributes->get('_route');
        if ($locationId !== null && $currentRoute === UrlAliasRouter::URL_ALIAS_ROUTE_NAME) {
            try {
                return $this->locationService->loadLocation((int)$locationId);
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }
    }
}
