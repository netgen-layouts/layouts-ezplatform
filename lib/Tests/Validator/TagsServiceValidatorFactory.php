<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\TagsBundle\API\Repository\TagsService;
use Netgen\BlockManager\Ez\Validator\TagValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class TagsServiceValidatorFactory extends ConstraintValidatorFactory
{
    /**
     * @var \Netgen\TagsBundle\API\Repository\TagsService
     */
    protected $tagsService;

    /**
     * Constructor.
     *
     * @param \Netgen\TagsBundle\API\Repository\TagsService $tagsService
     */
    public function __construct(TagsService $tagsService)
    {
        parent::__construct();

        $this->tagsService = $tagsService;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(Constraint $constraint)
    {
        $name = $constraint->validatedBy();

        if ($name === 'ngbm_eztag') {
            return new TagValidator($this->tagsService);
        }

        return parent::getInstance($constraint);
    }
}
