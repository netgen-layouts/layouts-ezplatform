<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target;

use Netgen\BlockManager\LayoutResolver\Target;

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
