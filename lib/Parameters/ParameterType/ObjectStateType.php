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
 * Parameter type used to store and validate an identifier of a object state in eZ Platform.
 */
final class ObjectStateType extends ParameterType
{
    public static function getIdentifier(): string
    {
        return 'ez_object_state';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefault('multiple', false);
        $optionsResolver->setDefault('states', []);

        $optionsResolver->setRequired(['multiple', 'states']);

        $optionsResolver->setAllowedTypes('multiple', 'bool');
        $optionsResolver->setAllowedTypes('states', 'array');
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

        $objectStateConstraints = [
            new Constraints\Type(['type' => 'string']),
            new EzConstraints\ObjectState(['allowedStates' => $parameterDefinition->getOption('states')]),
        ];

        if ($options['multiple'] === false) {
            return $objectStateConstraints;
        }

        return [
            new Constraints\Type(['type' => 'array']),
            new Constraints\All(
                [
                    'constraints' => $objectStateConstraints,
                ],
            ),
        ];
    }
}
