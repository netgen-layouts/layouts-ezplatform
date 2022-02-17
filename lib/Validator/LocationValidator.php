<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Validator;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Location as IbexaLocation;
use Netgen\Layouts\Ibexa\Validator\Constraint\Location;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function count;
use function in_array;
use function is_scalar;

/**
 * Validates if the provided value is an ID of a valid location in Ibexa Platform.
 */
final class LocationValidator extends ConstraintValidator
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof Location) {
            throw new UnexpectedTypeException($constraint, Location::class);
        }

        if (!is_scalar($value)) {
            throw new UnexpectedTypeException($value, 'scalar');
        }

        try {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
            $location = $this->repository->sudo(
                static fn (Repository $repository): IbexaLocation => $repository->getLocationService()->loadLocation((int) $value),
            );

            if (count($constraint->allowedTypes) > 0) {
                $contentType = $location->getContent()->getContentType();

                if (!in_array($contentType->identifier, $constraint->allowedTypes, true)) {
                    $this->context->buildViolation($constraint->typeNotAllowedMessage)
                        ->setParameter('%contentType%', $contentType->identifier)
                        ->addViolation();
                }
            }
        } catch (NotFoundException $e) {
            if (!$constraint->allowInvalid) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%locationId%', (string) $value)
                    ->addViolation();
            }
        }
    }
}
