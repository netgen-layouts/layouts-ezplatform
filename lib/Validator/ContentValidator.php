<?php

namespace Netgen\BlockManager\Ez\Validator;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use Netgen\BlockManager\Ez\Validator\Constraint\Content;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

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

        if (!$constraint instanceof Content) {
            throw new UnexpectedTypeException($constraint, Content::class);
        }

        if (!is_scalar($value)) {
            throw new UnexpectedTypeException($value, 'scalar');
        }

        try {
            $this->repository->sudo(
                function (Repository $repository) use ($value) {
                    $repository->getContentService()->loadContentInfo($value);
                }
            );
        } catch (NotFoundException $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%contentId%', $value)
                ->addViolation();
        }
    }
}
