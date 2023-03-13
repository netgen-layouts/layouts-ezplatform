<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Validator;

use Netgen\Layouts\Ibexa\Validator\Constraint\SiteAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function in_array;
use function is_string;

/**
 * Validates if the provided value is a valid Ibexa CMS siteaccess.
 */
final class SiteAccessValidator extends ConstraintValidator
{
    /**
     * @param string[] $siteAccessList
     */
    public function __construct(private array $siteAccessList)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof SiteAccess) {
            throw new UnexpectedTypeException($constraint, SiteAccess::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!in_array($value, $this->siteAccessList, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%siteAccess%', $value)
                ->addViolation();
        }
    }
}
