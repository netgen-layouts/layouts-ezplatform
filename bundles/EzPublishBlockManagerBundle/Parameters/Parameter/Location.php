<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints;
use Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint as EzConstraints;

class Location extends Parameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'ezlocation';
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getValueConstraints()
    {
        $constraints = array(
            new Constraints\Type(array('type' => 'numeric')),
            new Constraints\GreaterThan(array('value' => 0)),
            new EzConstraints\Location(),
        );

        return $constraints;
    }
}
