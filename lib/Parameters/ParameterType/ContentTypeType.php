<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Parameters\ParameterType;

use Netgen\Layouts\Ez\Validator\Constraint as EzConstraints;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

use function array_values;
use function is_array;

/**
 * Parameter type used to store and validate an identifier of a content type in eZ Platform.
 */
final class ContentTypeType extends ParameterType
{
    public static function getIdentifier(): string
    {
        return 'ez_content_type';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefault('multiple', false);
        $optionsResolver->setDefault('types', []);

        $optionsResolver->setRequired(['multiple', 'types']);

        $optionsResolver->setAllowedTypes('multiple', 'bool');
        $optionsResolver->setAllowedTypes('types', 'array');
    }

    public function fromHash(ParameterDefinition $parameterDefinition, $value)
    {
        if ($value === null || $value === []) {
            return null;
        }

        if ($parameterDefinition->getOption('multiple') === true) {
            return is_array($value) ? $value : [$value];
        }

        return is_array($value) ? array_values($value)[0] : $value;
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value): bool
    {
        return $value === null || $value === [];
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        $options = $parameterDefinition->getOptions();

        $contentTypeConstraints = [
            new Constraints\Type(['type' => 'string']),
            new EzConstraints\ContentType(['allowedTypes' => $parameterDefinition->getOption('types')]),
        ];

        if ($options['multiple'] === false) {
            return $contentTypeConstraints;
        }

        return [
            new Constraints\Type(['type' => 'array']),
            new Constraints\All(
                [
                    'constraints' => $contentTypeConstraints,
                ],
            ),
        ];
    }
}
