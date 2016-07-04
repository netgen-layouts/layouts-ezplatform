<?php

namespace Netgen\BlockManager\Ez\Validator;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class ContentValidator extends ConstraintValidator
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
                    $repository->getContentService()->loadContentInfo($value);
                }
            );
        } catch (NotFoundException $e) {
            /** @var \Netgen\BlockManager\Ez\Validator\Constraint\Content $constraint */
            $this->context->buildViolation($constraint->message)
                ->setParameter('%contentId%', $value)
                ->addViolation();
        }
    }
}
