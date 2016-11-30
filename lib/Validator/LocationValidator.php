<?php

namespace Netgen\BlockManager\Ez\Validator;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Netgen\BlockManager\Ez\Validator\Constraint\Location;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LocationValidator extends ConstraintValidator
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
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
            $this->repository->sudo(
                function (Repository $repository) use ($value) {
                    $repository->getLocationService()->loadLocation($value);
                }
            );
        } catch (NotFoundException $e) {
            /** @var \Netgen\BlockManager\Ez\Validator\Constraint\Location $constraint */
            $this->context->buildViolation($constraint->message)
                ->setParameter('%locationId%', $value)
                ->addViolation();
        }
    }
}
