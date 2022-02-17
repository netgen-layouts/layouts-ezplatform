<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Item\ValueConverter;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Item\ValueConverterInterface;

/**
 * @implements \Netgen\Layouts\Item\ValueConverterInterface<\Ibexa\Contracts\Core\Repository\Values\Content\Location>
 */
final class LocationValueConverter implements ValueConverterInterface
{
    public function supports(object $object): bool
    {
        return $object instanceof Location;
    }

    public function getValueType(object $object): string
    {
        return 'ibexa_location';
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
