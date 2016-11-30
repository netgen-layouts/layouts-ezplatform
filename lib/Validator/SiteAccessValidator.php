<?php

namespace Netgen\BlockManager\Ez\Validator;

use Netgen\BlockManager\Ez\Validator\Constraint\SiteAccess;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SiteAccessValidator extends ConstraintValidator
{
    /**
     * @var array
     */
    protected $siteAccessList;

    /**
     * Constructor.
     *
     * @param array $siteAccessList
     */
    public function __construct(array $siteAccessList)
    {
        $this->siteAccessList = $siteAccessList;
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

        if (!$constraint instanceof SiteAccess) {
            throw new UnexpectedTypeException($constraint, SiteAccess::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!in_array($value, $this->siteAccessList)) {
            /** @var \Netgen\BlockManager\Ez\Validator\Constraint\SiteAccess $constraint */
            $this->context->buildViolation($constraint->message)
                ->setParameter('%siteAccess%', $value)
                ->addViolation();
        }
    }
}
