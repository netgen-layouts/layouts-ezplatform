<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Item\ValueLoader;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Netgen\Layouts\Item\ValueLoaderInterface;
use Throwable;

final class ContentValueLoader implements ValueLoaderInterface
{
    public function __construct(private ContentService $contentService)
    {
    }

    public function load($id): ?ContentInfo
    {
        try {
            $contentInfo = $this->contentService->loadContentInfo((int) $id);
        } catch (Throwable) {
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
        } catch (Throwable) {
            return null;
        }

        if (!$contentInfo->published || $contentInfo->mainLocationId === null) {
            return null;
        }

        return $contentInfo;
    }
}
