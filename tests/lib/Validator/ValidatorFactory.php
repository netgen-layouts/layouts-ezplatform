<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\BlockManager\Ez\Validator\SiteAccessValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

class ValidatorFactory implements ConstraintValidatorFactoryInterface
{
    /**
     * @var \Symfony\Component\Validator\ConstraintValidatorFactoryInterface
     */
    protected $baseValidatorFactory;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->baseValidatorFactory = new ConstraintValidatorFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(Constraint $constraint)
    {
        $name = $constraint->validatedBy();

        if ($name === 'ngbm_ez_site_access') {
            return new SiteAccessValidator(array('eng', 'cro'));
        }

        return $this->baseValidatorFactory->getInstance($constraint);
    }
}
