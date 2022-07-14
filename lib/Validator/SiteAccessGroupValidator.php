<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Validator;

use Netgen\Layouts\Ez\Validator\Constraint\SiteAccessGroup;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function array_keys;
use function in_array;
use function is_string;

/**
 * Validates if the provided value is a valid eZ Platform siteaccess group.
 */
final class SiteAccessGroupValidator extends ConstraintValidator
{
    /**
     * @var string[]
     */
    private array $siteAccessGroupList;

    /**
     * @param array<string, string[]> $siteAccessGroupList
     */
    public function __construct(array $siteAccessGroupList)
    {
        $this->siteAccessGroupList = array_keys($siteAccessGroupList);
    }

    public function validate($value, Constraint $constraint): void
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

        if (!in_array($value, $this->siteAccessGroupList, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%siteAccessGroup%', $value)
                ->addViolation();
        }
    }
}
