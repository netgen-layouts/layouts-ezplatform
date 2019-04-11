<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Item\ValueConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Layouts\Item\ValueConverterInterface;

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

    public function supports(object $object): bool
    {
        return $object instanceof Location;
    }

    public function getValueType(object $object): string
    {
        return 'ezlocation';
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     *
     * @return int|string
     */
    public function getId(object $object)
    {
        return $object->id;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     *
     * @return int|string
     */
    public function getRemoteId(object $object)
    {
        return $object->remoteId;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     */
    public function getName(object $object): string
    {
        $versionInfo = $this->contentService->loadVersionInfo($object->getContentInfo());

        return $versionInfo->getName() ?? '';
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     */
    public function getIsVisible(object $object): bool
    {
        return !$object->invisible;
    }

    public function getObject(object $object): object
    {
        return $object;
    }
}
