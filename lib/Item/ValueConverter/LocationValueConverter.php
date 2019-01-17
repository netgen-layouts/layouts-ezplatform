<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Item\ValueConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\BlockManager\Item\ValueConverterInterface;

final class LocationValueConverter implements ValueConverterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function supports($object): bool
    {
        return $object instanceof Location;
    }

    public function getValueType($object): string
    {
        return 'ezlocation';
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     *
     * @return int|string
     */
    public function getId($object)
    {
        return $object->id;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     *
     * @return int|string
     */
    public function getRemoteId($object)
    {
        return $object->remoteId;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     */
    public function getName($object): string
    {
        $versionInfo = $this->contentService->loadVersionInfo($object->getContentInfo());

        return $versionInfo->getName() ?? '';
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     */
    public function getIsVisible($object): bool
    {
        return !$object->invisible;
    }

    public function getObject($object)
    {
        return $object;
    }
}
