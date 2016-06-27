<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Validator;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

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
        $location = $this->repository->sudo(
            function (Repository $repository) use ($value) {
                try {
                    return $repository->getLocationService()->loadLocation($value);
                } catch (NotFoundException $e) {
                    return;
                }
            }
        );

        /** @var \Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint\Location $constraint */
        if (!$location instanceof Location) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%locationId%', $value)
                ->addViolation();
        }
    }
}
