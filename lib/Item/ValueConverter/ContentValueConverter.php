<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Item\ValueConverter;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Netgen\Layouts\Item\ValueConverterInterface;

/**
 * @implements \Netgen\Layouts\Item\ValueConverterInterface<\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo>
 */
final class ContentValueConverter implements ValueConverterInterface
{
    public function __construct(
        private LocationService $locationService,
        private ContentService $contentService,
    ) {
    }

    public function supports(object $object): bool
    {
        return $object instanceof ContentInfo;
    }

    public function getValueType(object $object): string
    {
        return 'ibexa_content';
    }

    public function getId(object $object): int
    {
        return (int) $object->id;
    }

    public function getRemoteId(object $object): string
    {
        return $object->remoteId;
    }

    public function getName(object $object): string
    {
        $content = $this->contentService->loadContentByContentInfo($object);

        return $content->getName() ?? '';
    }

    public function getIsVisible(object $object): bool
    {
        $mainLocation = $this->locationService->loadLocation((int) $object->mainLocationId);

        return !$mainLocation->invisible;
    }

    public function getObject(object $object): ContentInfo
    {
        return $object;
    }
}
