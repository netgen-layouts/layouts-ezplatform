<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder;

use Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\Location as LocationTarget;
use Symfony\Component\HttpFoundation\Request;

class Location implements TargetBuilderInterface
{
    use RequestStackAwareTrait;

    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Target
     */
    public function buildTarget()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        if (!$currentRequest->attributes->has('locationId')) {
            return false;
        }

        return new LocationTarget(
            array($currentRequest->attributes->get('locationId'))
        );
    }
}
