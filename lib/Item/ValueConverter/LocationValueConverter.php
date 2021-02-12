<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Item\ValueConverter;

use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Layouts\Item\ValueConverterInterface;

/**
 * @implements \Netgen\Layouts\Item\ValueConverterInterface<\eZ\Publish\API\Repository\Values\Content\Location>
 */
final class LocationValueConverter implements ValueConverterInterface
{
    public function supports(object $object): bool
    {
        return $object instanceof Location;
    }

    public function getValueType(object $object): string
    {
        return 'ezlocation';
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
        return $object->getContent()->getName() ?? '';
    }

    public function getIsVisible(object $object): bool
    {
        return !$object->invisible;
    }

    public function getObject(object $object): Location
    {
        return $object;
    }
}
