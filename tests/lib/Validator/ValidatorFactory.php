<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\BlockManager\Ez\Validator\SiteAccessValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class ValidatorFactory extends ConstraintValidatorFactory
{
    /**
     * {@inheritdoc}
     */
    public function getInstance(Constraint $constraint)
    {
        $name = $constraint->validatedBy();

        if ($name === 'ngbm_ez_site_access') {
            return new SiteAccessValidator(array('eng', 'cro'));
        }

        return parent::getInstance($constraint);
    }
}
