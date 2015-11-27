<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target;

use Netgen\BlockManager\LayoutResolver\Target;

class Children extends Target
{
    /**
     * Returns the unique identifier of the target.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'children';
    }
}
