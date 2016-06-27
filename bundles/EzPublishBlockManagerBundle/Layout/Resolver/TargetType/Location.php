<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class Location implements TargetTypeInterface
{
    use RequestStackAwareTrait;

    /**
     * Returns the target type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'ezlocation';
    }

    /**
     * Provides the value for the target to be used in matching process.
     *
     * @return mixed
     */
    public function provideValue()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        if (!$currentRequest->attributes->has('locationId')) {
            return;
        }

        return $currentRequest->attributes->get('locationId');
    }
}
