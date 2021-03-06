<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator;

use Netgen\Layouts\Ez\Validator\SiteAccessGroupValidator;
use Netgen\Layouts\Ez\Validator\SiteAccessValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class ValidatorFactory implements ConstraintValidatorFactoryInterface
{
    private ConstraintValidatorFactory $baseValidatorFactory;

    /**
     * @var array<string, \Symfony\Component\Validator\ConstraintValidatorInterface>
     */
    private array $validators;

    public function __construct()
    {
        $this->baseValidatorFactory = new ConstraintValidatorFactory();

        $this->validators = [
            'nglayouts_ez_site_access' => new SiteAccessValidator(['eng', 'cro']),
            'nglayouts_ez_site_access_group' => new SiteAccessGroupValidator(
                [
                    'frontend' => ['eng'],
                    'backend' => ['admin'],
                ],
            ),
        ];
    }

    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $name = $constraint->validatedBy();

        return $this->validators[$name] ?? $this->baseValidatorFactory->getInstance($constraint);
    }
}
