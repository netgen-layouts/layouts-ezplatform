<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator;
use Netgen\BlockManager\Ez\Validator\SiteAccessValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

final class ValidatorFactory implements ConstraintValidatorFactoryInterface
{
    /**
     * @var \Symfony\Component\Validator\ConstraintValidatorFactoryInterface
     */
    private $baseValidatorFactory;

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

        if ($name === 'ngbm_ez_site_access_group') {
            return new SiteAccessGroupValidator(
                array(
                    'frontend' => array('eng'),
                    'backend' => array('admin'),
                )
            );
        }

        return $this->baseValidatorFactory->getInstance($constraint);
    }
}
