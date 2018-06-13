<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Item\ValueLoader;

use Exception;
use eZ\Publish\API\Repository\LocationService;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ValueLoaderInterface;

final class LocationValueLoader implements ValueLoaderInterface
{
    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function load($id)
    {
        try {
            $location = $this->locationService->loadLocation((int) $id);
        } catch (Exception $e) {
            throw new ItemException(
                sprintf('Location with ID "%s" could not be loaded.', $id),
                0,
                $e
            );
        }

        if (!$location->contentInfo->published) {
            throw new ItemException(
                sprintf('Location with ID "%s" has unpublished content and cannot be loaded.', $id)
            );
        }

        return $location;
    }

    public function loadByRemoteId($remoteId)
    {
        try {
            $location = $this->locationService->loadLocationByRemoteId((string) $remoteId);
        } catch (Exception $e) {
            throw new ItemException(
                sprintf('Location with remote ID "%s" could not be loaded.', $remoteId),
                0,
                $e
            );
        }

        if (!$location->contentInfo->published) {
            throw new ItemException(
                sprintf('Location with remote ID "%s" has unpublished content and cannot be loaded.', $remoteId)
            );
        }

        return $location;
    }
}
