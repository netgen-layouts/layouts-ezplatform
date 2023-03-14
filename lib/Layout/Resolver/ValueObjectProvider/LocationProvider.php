<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Layout\Resolver\ValueObjectProvider;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Layouts\Layout\Resolver\ValueObjectProviderInterface;

final class LocationProvider implements ValueObjectProviderInterface
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getValueObject($value): ?Location
    {
        try {
            return $this->repository->sudo(
                static fn (Repository $repository): Location => $repository->getLocationService()->loadLocation((int) $value),
            );
        } catch (NotFoundException $e) {
            return null;
        }
    }
}
