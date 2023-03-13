<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Validator;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Section as APISection;
use Netgen\Layouts\Ibexa\Validator\Constraint\Section;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function count;
use function in_array;
use function is_string;

/**
 * Validates if the provided value is an identifier of a valid section in Ibexa CMS.
 */
final class SectionValidator extends ConstraintValidator
{
    public function __construct(private Repository $repository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof Section) {
            throw new UnexpectedTypeException($constraint, Section::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        try {
            $this->repository->sudo(
                static fn (Repository $repository): APISection => $repository->getSectionService()->loadSectionByIdentifier($value),
            );
        } catch (NotFoundException) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%identifier%', $value)
                ->addViolation();

            return;
        }

        if (count($constraint->allowedSections) === 0) {
            return;
        }

        if (in_array($value, $constraint->allowedSections, true)) {
            return;
        }

        $this->context->buildViolation($constraint->notAllowedMessage)
            ->setParameter('%identifier%', $value)
            ->addViolation();
    }
}
