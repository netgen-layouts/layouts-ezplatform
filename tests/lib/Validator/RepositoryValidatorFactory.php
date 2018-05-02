<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\API\Repository\Repository;
use Netgen\BlockManager\Ez\Validator\ContentTypeValidator;
use Netgen\BlockManager\Ez\Validator\ContentValidator;
use Netgen\BlockManager\Ez\Validator\LocationValidator;
use Netgen\BlockManager\Ez\Validator\ObjectStateValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

final class RepositoryValidatorFactory implements ConstraintValidatorFactoryInterface
{
    /**
     * @var \Symfony\Component\Validator\ConstraintValidatorFactoryInterface
     */
    private $baseValidatorFactory;

    /**
     * @var array
     */
    private $validators = [];

    public function __construct(Repository $repository)
    {
        $this->baseValidatorFactory = new ConstraintValidatorFactory();

        $this->validators = [
            'ngbm_ezlocation' => new LocationValidator($repository),
            'ngbm_ezcontent' => new ContentValidator($repository),
            'ngbm_ez_content_type' => new ContentTypeValidator($repository),
            'ngbm_ez_object_state' => new ObjectStateValidator($repository),
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
