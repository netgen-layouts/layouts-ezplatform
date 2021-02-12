<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator;

use eZ\Publish\API\Repository\Repository;
use Netgen\Layouts\Ez\Validator\ContentTypeValidator;
use Netgen\Layouts\Ez\Validator\ContentValidator;
use Netgen\Layouts\Ez\Validator\LocationValidator;
use Netgen\Layouts\Ez\Validator\ObjectStateValidator;
use Netgen\Layouts\Ez\Validator\SectionValidator;
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
            'nglayouts_ez_location' => new LocationValidator($repository),
            'nglayouts_ez_content' => new ContentValidator($repository),
            'nglayouts_ez_content_type' => new ContentTypeValidator($repository),
            'nglayouts_ez_section' => new SectionValidator($repository),
            'nglayouts_ez_object_state' => new ObjectStateValidator($repository),
        ];
    }

    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $name = $constraint->validatedBy();

        return $this->validators[$name] ?? $this->baseValidatorFactory->getInstance($constraint);
    }
}
