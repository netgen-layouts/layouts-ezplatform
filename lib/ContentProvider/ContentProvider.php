<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\ContentProvider;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\BlockManager\Context\ContextInterface;

/**
 * Provides the eZ Platform content and location objects from the
 * current context.
 */
final class ContentProvider implements ContentProviderInterface
{
    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \Netgen\BlockManager\Context\ContextInterface
     */
    private $context;

    public function __construct(
        LocationService $locationService,
        ContentService $contentService,
        ContextInterface $context
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->context = $context;
    }

    public function provideContent()
    {
        $location = $this->loadLocation();
        if (!$location instanceof Location) {
            return null;
        }

        return $this->contentService->loadContent($location->contentId);
    }

    public function provideLocation()
    {
        return $this->loadLocation();
    }

    /**
     * Loads the location from the eZ Platform API by using the location ID
     * stored in the context.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    private function loadLocation()
    {
        if (!$this->context->has('ez_location_id')) {
            return null;
        }

        $location = $this->locationService->loadLocation(
            (int) $this->context->get('ez_location_id')
        );

        return $location;
    }
}
