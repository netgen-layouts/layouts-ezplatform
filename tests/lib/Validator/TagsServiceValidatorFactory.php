<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\BlockManager\Ez\Validator\TagValidator;
use Netgen\TagsBundle\API\Repository\TagsService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

final class TagsServiceValidatorFactory implements ConstraintValidatorFactoryInterface
{
    /**
     * @var \Symfony\Component\Validator\ConstraintValidatorFactoryInterface
     */
    private $baseValidatorFactory;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface[]
     */
    private $validators = [];

    public function __construct(TagsService $tagsService)
    {
        $this->baseValidatorFactory = new ConstraintValidatorFactory();

        $this->validators = [
            'ngbm_eztags' => new TagValidator($tagsService),
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
