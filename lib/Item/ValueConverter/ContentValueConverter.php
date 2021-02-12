<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Item\ValueConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\Layouts\Item\ValueConverterInterface;

/**
 * @implements \Netgen\Layouts\Item\ValueConverterInterface<\eZ\Publish\API\Repository\Values\Content\ContentInfo>
 */
final class ContentValueConverter implements ValueConverterInterface
{
    private LocationService $locationService;

    private ContentService $contentService;

    public function __construct(
        LocationService $locationService,
        ContentService $contentService
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
    }

    public function supports(object $object): bool
    {
        return $object instanceof ContentInfo;
    }

    public function getValueType(object $object): string
    {
        return 'ezcontent';
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
