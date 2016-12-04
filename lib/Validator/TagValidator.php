<?php

namespace Netgen\BlockManager\Ez\Validator;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Netgen\BlockManager\Ez\Validator\Constraint\Tag;
use Netgen\TagsBundle\API\Repository\TagsService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

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

        if (!$constraint instanceof Tag) {
            throw new UnexpectedTypeException($constraint, Tag::class);
        }

        if (!is_scalar($value)) {
            throw new UnexpectedTypeException($value, 'scalar');
        }

        try {
            $this->tagsService->sudo(
                function (TagsService $tagsService) use ($value) {
                    $tagsService->loadTag($value);
                }
            );
        } catch (NotFoundException $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%tagId%', $value)
                ->addViolation();
        }
    }
}
