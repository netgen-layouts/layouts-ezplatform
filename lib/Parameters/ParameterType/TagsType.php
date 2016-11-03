<?php

namespace Netgen\BlockManager\Ez\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\Validator\Constraints;
use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;

class TagsType extends ParameterType
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'eztags';
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
