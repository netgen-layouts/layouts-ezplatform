<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\BlockManager\Ez\Validator\TagValidator;
use Netgen\TagsBundle\API\Repository\TagsService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

class TagsServiceValidatorFactory implements ConstraintValidatorFactoryInterface
{
    /**
     * @var \Netgen\TagsBundle\API\Repository\TagsService
     */
    protected $tagsService;

    /**
     * @var \Symfony\Component\Validator\ConstraintValidatorFactoryInterface
     */
    protected $baseValidatorFactory;

    /**
     * Constructor.
     *
     * @param \Netgen\TagsBundle\API\Repository\TagsService $tagsService
     */
    public function __construct(TagsService $tagsService)
    {
        $this->tagsService = $tagsService;
        $this->baseValidatorFactory = new ConstraintValidatorFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(Constraint $constraint)
    {
        $name = $constraint->validatedBy();

        if ($name === 'ngbm_eztags') {
            return new TagValidator($this->tagsService);
        }

        return $this->baseValidatorFactory->getInstance($constraint);
    }
}
