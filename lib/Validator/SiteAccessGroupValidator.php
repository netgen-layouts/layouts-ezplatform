<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Validator;

use Netgen\Layouts\Ibexa\Validator\Constraint\SiteAccessGroup;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function array_key_exists;
use function is_string;

/**
 * Validates if the provided value is a valid Ibexa CMS siteaccess group.
 */
final class SiteAccessGroupValidator extends ConstraintValidator
{
    /**
     * @param array<string, string[]> $siteAccessGroupList
     */
    public function __construct(private array $siteAccessGroupList) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof SiteAccessGroup) {
            throw new UnexpectedTypeException($constraint, SiteAccessGroup::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!array_key_exists($value, $this->siteAccessGroupList)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%siteAccessGroup%', $value)
                ->addViolation();
        }
    }
}
