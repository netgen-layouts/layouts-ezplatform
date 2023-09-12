<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Validator;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Netgen\Layouts\Ibexa\Validator\Constraint\Tag;
use Netgen\TagsBundle\API\Repository\TagsService;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag as APITag;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function is_scalar;

/**
 * Validates if the provided value is an ID of a valid tag in Netgen Tags.
 */
final class TagValidator extends ConstraintValidator
{
    public function __construct(private TagsService $tagsService) {}

    public function validate(mixed $value, Constraint $constraint): void
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
                    fn (): APITag => $this->tagsService->loadTag((int) $value),
                );
            } catch (NotFoundException) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%tagId%', (string) $value)
                    ->addViolation();
            }
        }
    }
}
