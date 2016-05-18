<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target;

use Netgen\BlockManager\Layout\Resolver\Target;

class SemanticPathInfo extends Target
{
    /**
     * Returns the unique identifier of the target.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'semantic_path_info';
    }
}
