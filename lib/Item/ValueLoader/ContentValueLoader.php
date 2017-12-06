<?php

namespace Netgen\BlockManager\Ez\Item\ValueLoader;

use Exception;
use eZ\Publish\API\Repository\ContentService;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ValueLoaderInterface;

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
        } catch (Exception $e) {
            throw new ItemException(
                sprintf('Content with ID "%s" could not be loaded.', $id),
                0,
                $e
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
        } catch (Exception $e) {
            throw new ItemException(
                sprintf('Content with remote ID "%s" could not be loaded.', $remoteId),
                0,
                $e
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
