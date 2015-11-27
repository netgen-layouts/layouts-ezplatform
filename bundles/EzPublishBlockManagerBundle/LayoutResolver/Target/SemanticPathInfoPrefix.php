<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target;

use Netgen\BlockManager\LayoutResolver\Target;

class SemanticPathInfoPrefix extends Target
{
    /**
     * Returns the unique identifier of the target.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'semantic_path_info_prefix';
    }
}
