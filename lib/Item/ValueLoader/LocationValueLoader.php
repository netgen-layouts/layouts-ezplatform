<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Item\ValueLoader;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Layouts\Item\ValueLoaderInterface;
use Throwable;

final class LocationValueLoader implements ValueLoaderInterface
{
    private LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function load($id): ?Location
    {
        try {
            $location = $this->locationService->loadLocation((int) $id);
        } catch (Throwable $t) {
            return null;
        }

        return $location->contentInfo->published ? $location : null;
    }

    public function loadByRemoteId($remoteId): ?Location
    {
        try {
            $location = $this->locationService->loadLocationByRemoteId((string) $remoteId);
        } catch (Throwable $t) {
            return null;
        }

        return $location->contentInfo->published ? $location : null;
    }
}
