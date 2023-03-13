<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Item\ValueLoader;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Item\ValueLoaderInterface;
use Throwable;

final class LocationValueLoader implements ValueLoaderInterface
{
    public function __construct(private LocationService $locationService)
    {
    }

    public function load($id): ?Location
    {
        try {
            $location = $this->locationService->loadLocation((int) $id);
        } catch (Throwable) {
            return null;
        }

        return $location->contentInfo->published ? $location : null;
    }

    public function loadByRemoteId($remoteId): ?Location
    {
        try {
            $location = $this->locationService->loadLocationByRemoteId((string) $remoteId);
        } catch (Throwable) {
            return null;
        }

        return $location->contentInfo->published ? $location : null;
    }
}
