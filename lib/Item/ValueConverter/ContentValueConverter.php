<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Item\ValueConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\BlockManager\Item\ValueConverterInterface;

final class ContentValueConverter implements ValueConverterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    public function __construct(
        LocationService $locationService,
        ContentService $contentService
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
    }

    public function supports($object): bool
    {
        return $object instanceof ContentInfo;
    }

    public function getValueType($object): string
    {
        return 'ezcontent';
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $object
     *
     * @return int|string
     */
    public function getId($object)
    {
        return $object->id;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $object
     *
     * @return int|string
     */
    public function getRemoteId($object)
    {
        return $object->remoteId;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $object
     */
    public function getName($object): string
    {
        $versionInfo = $this->contentService->loadVersionInfo($object);

        return $versionInfo->getName() ?? '';
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $object
     */
    public function getIsVisible($object): bool
    {
        $mainLocation = $this->locationService->loadLocation($object->mainLocationId);

        return !$mainLocation->invisible;
    }

    public function getObject($object)
    {
        return $object;
    }
}
