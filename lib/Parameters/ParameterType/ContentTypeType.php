<?php

namespace Netgen\BlockManager\Ez\Parameters\ParameterType;

use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ContentTypeType extends ParameterType
{
    /**
     * Returns the parameter type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'ez_content_type';
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('multiple', false);
        $optionsResolver->setRequired(array('multiple'));
        $optionsResolver->setAllowedTypes('multiple', 'bool');
    }

    /**
     * Converts the provided parameter value to value usable by the domain.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return mixed
     */
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

    /**
     * Returns if the parameter value is empty.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return bool
     */
    public function isValueEmpty(ParameterInterface $parameter, $value)
    {
        return $value === null || $value === array();
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        $options = $parameter->getOptions();

        $contentTypeConstraints = array(
            new Constraints\Type(array('type' => 'string')),
            new EzConstraints\ContentType(),
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
