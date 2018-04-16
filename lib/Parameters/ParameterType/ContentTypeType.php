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
        $optionsResolver->setDefault('types', array());

        $optionsResolver->setRequired(array('multiple', 'types'));

        $optionsResolver->setAllowedTypes('multiple', 'bool');
        $optionsResolver->setAllowedTypes('types', 'array');
    }

    public function fromHash(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        if ($value === null || $value === array()) {
            return;
        }

        if ($parameterDefinition->getOption('multiple')) {
            return is_array($value) ? $value : array($value);
        }

        return is_array($value) ? array_values($value)[0] : $value;
    }

    public function isValueEmpty(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return $value === null || $value === array();
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        $options = $parameterDefinition->getOptions();

        $contentTypeConstraints = array(
            new Constraints\Type(array('type' => 'string')),
            new EzConstraints\ContentType(array('allowedTypes' => $parameterDefinition->getOption('types'))),
        );

        if (!$options['multiple']) {
            return $contentTypeConstraints;
        }

        return array(
            new Constraints\Type(array('type' => 'array')),
            new Constraints\All(
                array(
                    'constraints' => $contentTypeConstraints,
                )
            ),
        );
    }
}
