<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Layout\Resolver\ValueObjectProvider;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Layout\Resolver\ValueObjectProviderInterface;

final class LocationProvider implements ValueObjectProviderInterface
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getValueObject($value): ?object
    {
        try {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
            $location = $this->repository->sudo(
                static fn (Repository $repository): Location => $repository->getLocationService()->loadLocation((int) $value),
            );

            return $location;
        } catch (NotFoundException $e) {
            return null;
        }
    }
}
