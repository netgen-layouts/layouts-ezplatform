<?php

namespace Netgen\BlockManager\Ez\Parameters\ParameterType;

use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an identifier of a object state in eZ Platform.
 */
final class ObjectStateType extends ParameterType
{
    public function getIdentifier()
    {
        return 'ez_object_state';
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('multiple', false);
        $optionsResolver->setDefault('states', array());

        $optionsResolver->setRequired(array('multiple', 'states'));

        $optionsResolver->setAllowedTypes('multiple', 'bool');
        $optionsResolver->setAllowedTypes('states', 'array');
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

        $objectStateConstraints = array(
            new Constraints\Type(array('type' => 'string')),
            new EzConstraints\ObjectState(array('allowedStates' => $parameter->getOption('states'))),
        );

        if (!$options['multiple']) {
            return $objectStateConstraints;
        }

        return array(
            new Constraints\Type(array('type' => 'array')),
            new Constraints\All(
                array(
                    'constraints' => $objectStateConstraints,
                )
            ),
        );
    }
}
