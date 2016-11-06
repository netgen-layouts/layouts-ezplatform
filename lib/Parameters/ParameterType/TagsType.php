<?php

namespace Netgen\BlockManager\Ez\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;

class TagsType extends ParameterType
{
    /**
     * Returns the parameter type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'eztags';
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('min', null);
        $optionsResolver->setDefault('max', null);

        $optionsResolver->setRequired(array('min', 'max'));

        $optionsResolver->setAllowedTypes('min', array('int', 'null'));
        $optionsResolver->setAllowedTypes('max', array('int', 'null'));

        $optionsResolver->setAllowedValues('min', function ($value) {
            return $value === null || $value > 0;
        });

        $optionsResolver->setAllowedValues('max', function ($value) {
            return $value === null || $value > 0;
        });

        $optionsResolver->setNormalizer(
            'max',
            function (Options $options, $value) {
                if ($value === null || $options['min'] === null) {
                    return $value;
                }

                if ($value < $options['min']) {
                    return $options['min'];
                }

                return $value;
            }
        );
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

        $constraints = array(
            new Constraints\Type(array('type' => 'array')),
            new Constraints\All(
                array(
                    'constraints' => array(
                        new Constraints\NotBlank(),
                        new Constraints\Type(array('type' => 'numeric')),
                        new Constraints\GreaterThan(array('value' => 0)),
                        new EzConstraints\Tag(),
                    ),
                )
            ),
        );

        if ($options['min'] !== null || $options['max'] !== null) {
            $constraints[] = new Constraints\Count(
                array(
                    'min' => $options['min'] !== null ? $options['min'] : null,
                    'max' => $options['max'] !== null ? $options['max'] : null,
                )
            );
        }

        return $constraints;
    }
}
