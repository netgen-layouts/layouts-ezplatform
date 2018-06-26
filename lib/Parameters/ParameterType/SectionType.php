<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Parameters\ParameterType;

use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an identifier of a section in eZ Platform.
 */
final class SectionType extends ParameterType
{
    public function getIdentifier(): string
    {
        return 'ez_section';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefault('multiple', false);
        $optionsResolver->setDefault('sections', []);

        $optionsResolver->setRequired(['multiple', 'sections']);

        $optionsResolver->setAllowedTypes('multiple', 'bool');
        $optionsResolver->setAllowedTypes('sections', 'array');

        // @deprecated Replace with "string[]" allowed type when support for Symfony 2.8 ends
        $optionsResolver->setAllowedValues(
            'sections',
            function (array $sections): bool {
                foreach ($sections as $section) {
                    if (!is_string($section)) {
                        return false;
                    }
                }

                return true;
            }
        );
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
            new EzConstraints\Section(['allowedSections' => $parameterDefinition->getOption('sections')]),
        ];

        if (!$options['multiple']) {
            return $sectionConstraints;
        }

        return [
            new Constraints\Type(['type' => 'array']),
            new Constraints\All(
                [
                    'constraints' => $sectionConstraints,
                ]
            ),
        ];
    }
}
