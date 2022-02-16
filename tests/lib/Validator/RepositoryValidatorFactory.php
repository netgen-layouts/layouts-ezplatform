<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator;

use Ibexa\Contracts\Core\Repository\Repository;
use Netgen\Layouts\Ibexa\Validator\ContentTypeValidator;
use Netgen\Layouts\Ibexa\Validator\ContentValidator;
use Netgen\Layouts\Ibexa\Validator\LocationValidator;
use Netgen\Layouts\Ibexa\Validator\ObjectStateValidator;
use Netgen\Layouts\Ibexa\Validator\SectionValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class RepositoryValidatorFactory implements ConstraintValidatorFactoryInterface
{
    private ConstraintValidatorFactory $baseValidatorFactory;

    /**
     * @var array<string, \Symfony\Component\Validator\ConstraintValidatorInterface>
     */
    private array $validators;

    public function __construct(Repository $repository)
    {
        $this->baseValidatorFactory = new ConstraintValidatorFactory();

        $this->validators = [
            'nglayouts_ibexa_location' => new LocationValidator($repository),
            'nglayouts_ibexa_content' => new ContentValidator($repository),
            'nglayouts_ibexa_content_type' => new ContentTypeValidator($repository),
            'nglayouts_ibexa_section' => new SectionValidator($repository),
            'nglayouts_ibexa_object_state' => new ObjectStateValidator($repository),
        ];
    }

    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $name = $constraint->validatedBy();

        return $this->validators[$name] ?? $this->baseValidatorFactory->getInstance($constraint);
    }
}
