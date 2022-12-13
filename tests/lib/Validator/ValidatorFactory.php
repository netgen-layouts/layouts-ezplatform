<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator;

use eZ\Publish\API\Repository\Repository;
use Netgen\Layouts\Ez\Validator\ContentValidator;
use Netgen\Layouts\Ez\Validator\SiteAccessGroupValidator;
use Netgen\Layouts\Ez\Validator\SiteAccessValidator;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory as BaseValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class ValidatorFactory implements ConstraintValidatorFactoryInterface
{
    private TestCase $testCase;

    private BaseValidatorFactory $baseValidatorFactory;

    /**
     * @var array<string, \Symfony\Component\Validator\ConstraintValidatorInterface>
     */
    private array $validators;

    public function __construct(TestCase $testCase, BaseValidatorFactory $baseValidatorFactory)
    {
        $this->testCase = $testCase;
        $this->baseValidatorFactory = $baseValidatorFactory;

        $this->validators = [
            'nglayouts_ez_site_access' => new SiteAccessValidator(['eng', 'cro']),
            'nglayouts_ez_site_access_group' => new SiteAccessGroupValidator(
                [
                    'frontend' => ['eng'],
                    'backend' => ['admin'],
                ],
            ),
            'nglayouts_ez_content' => new ContentValidator(
                $this->testCase
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock(),
            ),
        ];
    }

    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $name = $constraint->validatedBy();

        return $this->validators[$name] ?? $this->baseValidatorFactory->getInstance($constraint);
    }
}
