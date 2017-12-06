<?php

namespace Netgen\BlockManager\Ez\Parameters\ParameterType;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Parameters\ParameterInterface;
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

        $optionsResolver->setRequired(array('min', 'max', 'allow_invalid'));

        $optionsResolver->setAllowedTypes('min', array('int', 'null'));
        $optionsResolver->setAllowedTypes('max', array('int', 'null'));
        $optionsResolver->setAllowedTypes('allow_invalid', array('bool'));

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

    public function export(ParameterInterface $parameter, $value)
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
            // Do nothing
        }
    }

    public function import(ParameterInterface $parameter, $value)
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
            // Do nothing
        }
    }

    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        $options = $parameter->getOptions();

        $constraints = array(
            new Constraints\Type(array('type' => 'array')),
            new Constraints\All(
                array(
                    'constraints' => array(
                        new Constraints\NotBlank(),
                        new Constraints\Type(array('type' => 'numeric')),
                        new Constraints\GreaterThan(array('value' => 0)),
                        new EzConstraints\Tag(array('allowInvalid' => $options['allow_invalid'])),
                    ),
                )
            ),
        );

        if ($options['min'] !== null || $options['max'] !== null) {
            $constraints[] = new Constraints\Count(
                array(
                    'min' => $options['min'] !== null ? $options['min'] : null,
                    'max' => $options['max'] !== null ? $options['max'] : null,
                )
            );
        }

        return $constraints;
    }
}
