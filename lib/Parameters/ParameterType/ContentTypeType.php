<?php

namespace Netgen\BlockManager\Ez\Parameters\ParameterType;

use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an identifier of a content type in eZ Platform.
 */
final class ContentTypeType extends ParameterType
{
    public function getIdentifier()
    {
        return 'ez_content_type';
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('multiple', false);
        $optionsResolver->setDefault('types', []);

        $optionsResolver->setRequired(['multiple', 'types']);

        $optionsResolver->setAllowedTypes('multiple', 'bool');
        $optionsResolver->setAllowedTypes('types', 'array');
    }

    public function fromHash(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        if ($value === null || $value === []) {
            return;
        }

        if ($parameterDefinition->getOption('multiple')) {
            return is_array($value) ? $value : [$value];
        }

        return is_array($value) ? array_values($value)[0] : $value;
    }

    public function isValueEmpty(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return $value === null || $value === [];
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        $options = $parameterDefinition->getOptions();

        $contentTypeConstraints = [
            new Constraints\Type(['type' => 'string']),
            new EzConstraints\ContentType(['allowedTypes' => $parameterDefinition->getOption('types')]),
        ];

        if (!$options['multiple']) {
            return $contentTypeConstraints;
        }

        return [
            new Constraints\Type(['type' => 'array']),
            new Constraints\All(
                [
                    'constraints' => $contentTypeConstraints,
                ]
            ),
        ];
    }
}
