<?php

namespace Netgen\BlockManager\Ez\Validator;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class ContentTypeValidator extends ConstraintValidator
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

        try {
            $this->repository->sudo(
                function (Repository $repository) use ($value) {
                    $repository->getContentTypeService()->loadContentTypeByIdentifier($value);
                }
            );
        } catch (NotFoundException $e) {
            /** @var \Netgen\BlockManager\Ez\Validator\Constraint\ContentType $constraint */
            $this->context->buildViolation($constraint->message)
                ->setParameter('%identifier%', $value)
                ->addViolation();
        }
    }
}
