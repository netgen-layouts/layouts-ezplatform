<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target;

use Netgen\BlockManager\Layout\Resolver\Target;

class Location extends Target
{
    /**
     * Returns the unique identifier of the target.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'location';
    }
}
