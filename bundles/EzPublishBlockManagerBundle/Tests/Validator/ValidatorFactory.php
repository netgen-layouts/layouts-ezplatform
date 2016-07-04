<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Validator;

use Netgen\Bundle\EzPublishBlockManagerBundle\Validator\SiteAccessValidator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Constraint;

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
        } else {
            return parent::getInstance($constraint);
        }
    }
}
