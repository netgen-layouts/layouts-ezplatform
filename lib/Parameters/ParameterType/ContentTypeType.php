<?php

namespace Netgen\BlockManager\Ez\Parameters\ParameterType;

use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ContentTypeType extends ParameterType
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

    public function fromHash(ParameterInterface $parameter, $value)
    {
        if ($value === null || $value === array()) {
            return null;
        }

        if ($parameter->getOption('multiple')) {
            return is_array($value) ? $value : array($value);
        }

        return is_array($value) ? array_values($value)[0] : $value;
    }

    public function isValueEmpty(ParameterInterface $parameter, $value)
    {
        return $value === null || $value === array();
    }

    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        $options = $parameter->getOptions();

        $contentTypeConstraints = array(
            new Constraints\Type(array('type' => 'string')),
            new EzConstraints\ContentType(array('allowedTypes' => $parameter->getOption('types'))),
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
