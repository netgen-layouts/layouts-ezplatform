<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Validator;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Netgen\BlockManager\Ez\Validator\Constraint\Tag;
use Netgen\TagsBundle\API\Repository\TagsService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if the provided value is an ID of a valid tag in Netgen Tags.
 */
final class TagValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\TagsBundle\API\Repository\TagsService
     */
    private $tagsService;

    public function __construct(TagsService $tagsService)
    {
        $this->tagsService = $tagsService;
    }

    public function validate($value, Constraint $constraint): void
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

        if (!$constraint->allowInvalid) {
            try {
                $this->tagsService->sudo(
                    static function (TagsService $tagsService) use ($value): void {
                        $tagsService->loadTag($value);
                    }
                );
            } catch (NotFoundException $e) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%tagId%', (string) $value)
                    ->addViolation();
            }
        }
    }
}
