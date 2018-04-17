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
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface[]
     */
    private $validators = [];

    public function __construct()
    {
        $this->baseValidatorFactory = new ConstraintValidatorFactory();

        $this->validators = [
            'ngbm_ez_site_access' => new SiteAccessValidator(['eng', 'cro']),
            'ngbm_ez_site_access_group' => new SiteAccessGroupValidator(
                [
                    'frontend' => ['eng'],
                    'backend' => ['admin'],
                ]
            ),
        ];
    }

    public function getInstance(Constraint $constraint)
    {
        $name = $constraint->validatedBy();

        if (isset($this->validators[$name])) {
            return $this->validators[$name];
        }

        return $this->baseValidatorFactory->getInstance($constraint);
    }
}
