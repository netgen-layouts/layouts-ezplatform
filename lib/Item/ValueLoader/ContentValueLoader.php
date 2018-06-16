<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Item\ValueLoader;

use eZ\Publish\API\Repository\ContentService;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ValueLoaderInterface;
use Throwable;

final class ContentValueLoader implements ValueLoaderInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function load($id)
    {
        try {
            $contentInfo = $this->contentService->loadContentInfo((int) $id);
        } catch (Throwable $t) {
            throw new ItemException(
                sprintf('Content with ID "%s" could not be loaded.', $id),
                0,
                $t
            );
        }

        if (!$contentInfo->published) {
            throw new ItemException(
                sprintf('Content with ID "%s" is not published and cannot loaded.', $id)
            );
        }

        if ($contentInfo->mainLocationId === null) {
            throw new ItemException(
                sprintf('Content with ID "%s" does not have a main location and cannot loaded.', $id)
            );
        }

        return $contentInfo;
    }

    public function loadByRemoteId($remoteId)
    {
        try {
            $contentInfo = $this->contentService->loadContentInfoByRemoteId((string) $remoteId);
        } catch (Throwable $t) {
            throw new ItemException(
                sprintf('Content with remote ID "%s" could not be loaded.', $remoteId),
                0,
                $t
            );
        }

        if (!$contentInfo->published) {
            throw new ItemException(
                sprintf('Content with remote ID "%s" is not published and cannot loaded.', $remoteId)
            );
        }

        if ($contentInfo->mainLocationId === null) {
            throw new ItemException(
                sprintf('Content with remote ID "%s" does not have a main location and cannot loaded.', $remoteId)
            );
        }

        return $contentInfo;
    }
}
