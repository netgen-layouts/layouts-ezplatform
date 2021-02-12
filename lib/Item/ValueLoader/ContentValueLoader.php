<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Item\ValueLoader;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\Layouts\Item\ValueLoaderInterface;
use Throwable;

final class ContentValueLoader implements ValueLoaderInterface
{
    private ContentService $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function load($id): ?ContentInfo
    {
        try {
            $contentInfo = $this->contentService->loadContentInfo((int) $id);
        } catch (Throwable $t) {
            return null;
        }

        if (!$contentInfo->published || $contentInfo->mainLocationId === null) {
            return null;
        }

        return $contentInfo;
    }

    public function loadByRemoteId($remoteId): ?ContentInfo
    {
        try {
            $contentInfo = $this->contentService->loadContentInfoByRemoteId((string) $remoteId);
        } catch (Throwable $t) {
            return null;
        }

        if (!$contentInfo->published || $contentInfo->mainLocationId === null) {
            return null;
        }

        return $contentInfo;
    }
}
