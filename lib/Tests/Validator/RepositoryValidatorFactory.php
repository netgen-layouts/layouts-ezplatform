<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\API\Repository\Repository;
use Netgen\BlockManager\Ez\Validator\ContentTypeValidator;
use Netgen\BlockManager\Ez\Validator\ContentValidator;
use Netgen\BlockManager\Ez\Validator\LocationValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class RepositoryValidatorFactory extends ConstraintValidatorFactory
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\Repository $repository
     */
    public function __construct(Repository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(Constraint $constraint)
    {
        $name = $constraint->validatedBy();

        if ($name === 'ngbm_ezlocation') {
            return new LocationValidator($this->repository);
        } elseif ($name === 'ngbm_ezcontent') {
            return new ContentValidator($this->repository);
        } elseif ($name === 'ngbm_ez_content_type') {
            return new ContentTypeValidator($this->repository);
        } else {
            return parent::getInstance($constraint);
        }
    }
}
