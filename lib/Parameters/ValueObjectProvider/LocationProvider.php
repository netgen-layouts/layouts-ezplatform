<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Parameters\ValueObjectProvider;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Parameters\ValueObjectProviderInterface;

final class LocationProvider implements ValueObjectProviderInterface
{
    public function __construct(private Repository $repository)
    {
    }

    public function getValueObject(mixed $value): ?Location
    {
        try {
            return $this->repository->sudo(
                fn (): Location => $this->repository->getLocationService()->loadLocation((int) $value),
            );
        } catch (NotFoundException) {
            return null;
        }
    }
}
