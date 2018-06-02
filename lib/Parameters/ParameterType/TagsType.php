<?php

namespace Netgen\BlockManager\Ez\Parameters\ParameterType;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\TagsBundle\API\Repository\TagsService;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an ID of a tag in Netgen Tags.
 */
final class TagsType extends ParameterType
{
    /**
     * @var \Netgen\TagsBundle\API\Repository\TagsService
     */
    private $tagsService;

    public function __construct(TagsService $tagsService)
    {
        $this->tagsService = $tagsService;
    }

    public function getIdentifier()
    {
        return 'eztags';
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('min', null);
        $optionsResolver->setDefault('max', null);
        $optionsResolver->setDefault('allow_invalid', false);

        $optionsResolver->setRequired(['min', 'max', 'allow_invalid']);

        $optionsResolver->setAllowedTypes('min', ['int', 'null']);
        $optionsResolver->setAllowedTypes('max', ['int', 'null']);
        $optionsResolver->setAllowedTypes('allow_invalid', ['bool']);

        $optionsResolver->setAllowedValues('min', function ($value) {
            return $value === null || $value > 0;
        });

        $optionsResolver->setAllowedValues('max', function ($value) {
            return $value === null || $value > 0;
        });

        $optionsResolver->setNormalizer(
            'max',
            function (Options $options, $value) {
                if ($value === null || $options['min'] === null) {
                    return $value;
                }

                if ($value < $options['min']) {
                    return $options['min'];
                }

                return $value;
            }
        );
    }

    public function export(ParameterDefinition $parameterDefinition, $value)
    {
        try {
            /** @var \Netgen\TagsBundle\API\Repository\Values\Tags\Tag $tag */
            $tag = $this->tagsService->sudo(
                function (TagsService $tagsService) use ($value) {
                    return $tagsService->loadTag($value);
                }
            );

            return $tag->remoteId;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function import(ParameterDefinition $parameterDefinition, $value)
    {
        try {
            /** @var \Netgen\TagsBundle\API\Repository\Values\Tags\Tag $tag */
            $tag = $this->tagsService->sudo(
                function (TagsService $tagsService) use ($value) {
                    return $tagsService->loadTagByRemoteId($value);
                }
            );

            return $tag->id;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value)
    {
        $options = $parameterDefinition->getOptions();

        $constraints = [
            new Constraints\Type(['type' => 'array']),
            new Constraints\All(
                [
                    'constraints' => [
                        new Constraints\NotBlank(),
                        new Constraints\Type(['type' => 'numeric']),
                        new Constraints\GreaterThan(['value' => 0]),
                        new EzConstraints\Tag(['allowInvalid' => $options['allow_invalid']]),
                    ],
                ]
            ),
        ];

        if ($options['min'] !== null || $options['max'] !== null) {
            $constraints[] = new Constraints\Count(
                [
                    'min' => $options['min'] !== null ? $options['min'] : null,
                    'max' => $options['max'] !== null ? $options['max'] : null,
                ]
            );
        }

        return $constraints;
    }
}
