<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Parameters\ParameterType;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Netgen\Layouts\Ibexa\Validator\Constraint as IbexaConstraints;
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
    public function __construct(private TagsService $tagsService) {}

    public static function getIdentifier(): string
    {
        return 'netgen_tags';
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
            static fn (Options $options, ?int $value): ?int => match (true) {
                $value === null || $options['min'] === null => $value,
                $value < $options['min'] => $options['min'],
                default => $value,
            },
        );
    }

    public function fromHash(ParameterDefinition $parameterDefinition, mixed $value)
    {
        return is_array($value) ? array_map('intval', $value) : $value;
    }

    public function export(ParameterDefinition $parameterDefinition, mixed $value): ?string
    {
        try {
            /** @var \Netgen\TagsBundle\API\Repository\Values\Tags\Tag $tag */
            $tag = $this->tagsService->sudo(
                fn (): Tag => $this->tagsService->loadTag((int) $value),
            );

            return $tag->remoteId;
        } catch (NotFoundException) {
            return null;
        }
    }

    public function import(ParameterDefinition $parameterDefinition, mixed $value): ?int
    {
        try {
            /** @var \Netgen\TagsBundle\API\Repository\Values\Tags\Tag $tag */
            $tag = $this->tagsService->sudo(
                fn (): Tag => $this->tagsService->loadTagByRemoteId((string) $value),
            );

            return $tag->id;
        } catch (NotFoundException) {
            return null;
        }
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
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
                        new IbexaConstraints\Tag(['allowInvalid' => $options['allow_invalid']]),
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
