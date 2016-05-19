<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder;

use Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\BlockManager\Layout\Resolver\Target;
use Symfony\Component\HttpFoundation\Request;

class Location implements TargetBuilderInterface
{
    use RequestStackAwareTrait;

    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Target|null
     */
    public function buildTarget()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        if (!$currentRequest->attributes->has('locationId')) {
            return;
        }

        return new Target(
            'location',
            array($currentRequest->attributes->get('locationId'))
        );
    }
}
