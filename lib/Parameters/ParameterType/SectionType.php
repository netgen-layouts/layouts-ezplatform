<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Parameters\ParameterType;

use Netgen\Layouts\Ibexa\Validator\Constraint as IbexaConstraints;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

use function array_values;
use function is_array;

/**
 * Parameter type used to store and validate an identifier of a section in Ibexa Platform.
 */
final class SectionType extends ParameterType
{
    public static function getIdentifier(): string
    {
        return 'ibexa_section';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefault('multiple', false);
        $optionsResolver->setDefault('sections', []);

        $optionsResolver->setRequired(['multiple', 'sections']);

        $optionsResolver->setAllowedTypes('multiple', 'bool');
        $optionsResolver->setAllowedTypes('sections', 'string[]');
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

        $sectionConstraints = [
            new Constraints\Type(['type' => 'string']),
            new IbexaConstraints\Section(['allowedSections' => $parameterDefinition->getOption('sections')]),
        ];

        if ($options['multiple'] === false) {
            return $sectionConstraints;
        }

        return [
            new Constraints\Type(['type' => 'array']),
            new Constraints\All(
                [
                    'constraints' => $sectionConstraints,
                ],
            ),
        ];
    }
}
