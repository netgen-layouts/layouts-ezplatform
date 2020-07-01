<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Utils;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;

final class RemoteIdConverter
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function toLocationId(string $remoteId): ?int
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $this->repository->sudo(
                static function (Repository $repository) use ($remoteId): Location {
                    return $repository->getLocationService()->loadLocationByRemoteId($remoteId);
                }
            );

            return (int) $location->id;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function toLocationRemoteId(int $id): ?string
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $this->repository->sudo(
                static function (Repository $repository) use ($id): Location {
                    return $repository->getLocationService()->loadLocation($id);
                }
            );

            return $location->remoteId;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function toContentId(string $remoteId): ?int
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo */
            $contentInfo = $this->repository->sudo(
                static function (Repository $repository) use ($remoteId): ContentInfo {
                    return $repository->getContentService()->loadContentInfoByRemoteId($remoteId);
                }
            );

            return $contentInfo->id;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function toContentRemoteId(int $id): ?string
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo */
            $contentInfo = $this->repository->sudo(
                static function (Repository $repository) use ($id): ContentInfo {
                    return $repository->getContentService()->loadContentInfo($id);
                }
            );

            return $contentInfo->remoteId;
        } catch (NotFoundException $e) {
            return null;
        }
    }
}
