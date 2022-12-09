<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\ContentProvider;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Context\Context;

/**
 * Provides the Ibexa CMS content and location objects from the
 * current context.
 */
final class ContentProvider implements ContentProviderInterface
{
    private LocationService $locationService;

    private Context $context;

    public function __construct(LocationService $locationService, Context $context)
    {
        $this->locationService = $locationService;
        $this->context = $context;
    }

    public function provideContent(): ?Content
    {
        $location = $this->loadLocation();
        if (!$location instanceof Location) {
            return null;
        }

        return $location->getContent();
    }

    public function provideLocation(): ?Location
    {
        return $this->loadLocation();
    }

    /**
     * Loads the location from the Ibexa CMS API by using the location ID
     * stored in the context.
     */
    private function loadLocation(): ?Location
    {
        if (!$this->context->has('ibexa_location_id')) {
            return null;
        }

        return $this->locationService->loadLocation(
            (int) $this->context->get('ibexa_location_id'),
        );
    }
}
