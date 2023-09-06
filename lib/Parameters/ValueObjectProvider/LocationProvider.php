<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Parameters\ValueObjectProvider;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Layouts\Error\ErrorHandlerInterface;
use Netgen\Layouts\Parameters\ValueObjectProviderInterface;

final class LocationProvider implements ValueObjectProviderInterface
{
    private Repository $repository;

    private ErrorHandlerInterface $errorHandler;

    public function __construct(Repository $repository, ErrorHandlerInterface $errorHandler)
    {
        $this->repository = $repository;
        $this->errorHandler = $errorHandler;
    }

    public function getValueObject($value): ?Location
    {
        if ($value === null) {
            return null;
        }

        try {
            return $this->repository->sudo(
                static fn (Repository $repository): Location => $repository->getLocationService()->loadLocation((int) $value),
            );
        } catch (NotFoundException $e) {
            $this->errorHandler->logError($e);

            return null;
        }
    }
}
