<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType;

class SemanticPathInfoPrefix extends SemanticPathInfo
{
    /**
     * Returns the target type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'ez_semantic_path_info_prefix';
    }
}
