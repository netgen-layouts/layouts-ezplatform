<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Parameters\ParameterType;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Netgen\Layouts\Ez\Validator\Constraint as EzConstraints;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\TagsBundle\API\Repository\TagsService;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

use function array_map;
use function is_array;

/**
 * Parameter type used to store and validate an ID of a tag in Netgen Tags.
 */
final class TagsType extends ParameterType
{
    private TagsService $tagsService;

    public function __construct(TagsService $tagsService)
    {
        $this->tagsService = $tagsService;
    }

    public static function getIdentifier(): string
    {
        return 'ez_tags';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefault('min', null);
        $optionsResolver->setDefault('max', null);
        $optionsResolver->setDefault('allow_invalid', false);

        $optionsResolver->setRequired(['min', 'max', 'allow_invalid']);

        $optionsResolver->setAllowedTypes('min', ['int', 'null']);
        $optionsResolver->setAllowedTypes('max', ['int', 'null']);
        $optionsResolver->setAllowedTypes('allow_invalid', 'bool');

        $optionsResolver->setAllowedValues(
            'min',
            static fn (?int $value): bool => $value === null || $value > 0,
        );

        $optionsResolver->setAllowedValues(
            'max',
            static fn (?int $value): bool => $value === null || $value > 0,
        );

        $optionsResolver->setNormalizer(
            'max',
            static function (Options $options, ?int $value): ?int {
                if ($value === null || $options['min'] === null) {
                    return $value;
                }

                if ($value < $options['min']) {
                    return $options['min'];
                }

                return $value;
            },
        );
    }

    public function fromHash(ParameterDefinition $parameterDefinition, $value)
    {
        return is_array($value) ? array_map('intval', $value) : $value;
    }

    public function export(ParameterDefinition $parameterDefinition, $value): ?string
    {
        try {
            /** @var \Netgen\TagsBundle\API\Repository\Values\Tags\Tag $tag */
            $tag = $this->tagsService->sudo(
                static fn (TagsService $tagsService): Tag => $tagsService->loadTag((int) $value),
            );

            return $tag->remoteId;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function import(ParameterDefinition $parameterDefinition, $value): ?int
    {
        try {
            /** @var \Netgen\TagsBundle\API\Repository\Values\Tags\Tag $tag */
            $tag = $this->tagsService->sudo(
                static fn (TagsService $tagsService): Tag => $tagsService->loadTagByRemoteId((string) $value),
            );

            return (int) $tag->id;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
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
                ],
            ),
        ];

        if ($options['min'] !== null || $options['max'] !== null) {
            $constraints[] = new Constraints\Count(
                [
                    'min' => $options['min'],
                    'max' => $options['max'],
                ],
            );
        }

        return $constraints;
    }
}
