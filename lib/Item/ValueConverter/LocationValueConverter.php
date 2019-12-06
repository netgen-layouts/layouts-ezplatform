<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Item\ValueConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Layouts\Item\ValueConverterInterface;

/**
 * @implements \Netgen\Layouts\Item\ValueConverterInterface<\eZ\Publish\API\Repository\Values\Content\Location>
 */
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

    public function getId(object $object)
    {
        return $object->id;
    }

    public function getRemoteId(object $object)
    {
        return $object->remoteId;
    }

    public function getName(object $object): string
    {
        $versionInfo = $this->contentService->loadVersionInfo($object->getContentInfo());

        return $versionInfo->getName() ?? '';
    }

    public function getIsVisible(object $object): bool
    {
        return !$object->invisible;
    }

    public function getObject(object $object): object
    {
        return $object;
    }
}
