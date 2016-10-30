<?php

namespace Netgen\BlockManager\Ez\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;

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
}
