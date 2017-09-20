<?php

namespace Netgen\BlockManager\Ez\Parameters\ParameterType;

use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an ID of a location in eZ Platform.
 */
class LocationType extends ParameterType
{
    public function getIdentifier()
    {
        return 'ezlocation';
    }

    public function isValueEmpty(ParameterInterface $parameter, $value)
    {
        return $value === null;
    }

    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        return array(
            new Constraints\Type(array('type' => 'numeric')),
            new Constraints\GreaterThan(array('value' => 0)),
            new EzConstraints\Location(),
        );
    }
}
