<?php

namespace Netgen\BlockManager\Ez\Validator;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Netgen\TagsBundle\API\Repository\TagsService;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class TagValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\TagsBundle\API\Repository\TagsService
     */
    protected $tagsService;

    /**
     * Constructor.
     *
     * @param \Netgen\TagsBundle\API\Repository\TagsService $tagsService
     */
    public function __construct(TagsService $tagsService)
    {
        $this->tagsService = $tagsService;
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
            $this->tagsService->sudo(
                function (TagsService $tagsService) use ($value) {
                    $tagsService->loadTag($value);
                }
            );
        } catch (NotFoundException $e) {
            /** @var \Netgen\BlockManager\Ez\Validator\Constraint\Tag $constraint */
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
