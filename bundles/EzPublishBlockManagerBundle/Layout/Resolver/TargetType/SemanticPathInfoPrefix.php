<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType;

class SemanticPathInfoPrefix extends SemanticPathInfo
{
    /**
     * Returns the target type.
     *
     * @return string
     */
    public function getType()
    {
        return 'ez_semantic_path_info_prefix';
    }
}
