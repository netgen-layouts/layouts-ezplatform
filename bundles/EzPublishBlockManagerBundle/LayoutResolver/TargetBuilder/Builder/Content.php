<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder;

use Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\Content as ContentTarget;
use Symfony\Component\HttpFoundation\Request;

class Content implements TargetBuilderInterface
{
    use RequestStackAwareTrait;

    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Target
     */
    public function buildTarget()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        if (!$currentRequest->attributes->has('contentId')) {
            return false;
        }

        return new ContentTarget(
            array($currentRequest->attributes->get('contentId'))
        );
    }
}
