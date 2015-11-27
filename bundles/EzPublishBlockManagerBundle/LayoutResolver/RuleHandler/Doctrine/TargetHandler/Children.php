<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

class Children extends Location
{
    /**
     * Returns the target identifier this handler handles.
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return 'children';
    }
}
