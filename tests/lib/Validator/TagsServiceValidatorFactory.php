<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator;

use Netgen\Layouts\Ez\Validator\TagValidator;
use Netgen\TagsBundle\API\Repository\TagsService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class TagsServiceValidatorFactory implements ConstraintValidatorFactoryInterface
{
    /**
     * @var \Symfony\Component\Validator\ConstraintValidatorFactoryInterface
     */
    private $baseValidatorFactory;

    /**
     * @var array
     */
    private $validators;

    public function __construct(TagsService $tagsService)
    {
        $this->baseValidatorFactory = new ConstraintValidatorFactory();

        $this->validators = [
            'nglayouts_eztags' => new TagValidator($tagsService),
        ];
    }

    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $name = $constraint->validatedBy();

        return $this->validators[$name] ?? $this->baseValidatorFactory->getInstance($constraint);
    }
}
