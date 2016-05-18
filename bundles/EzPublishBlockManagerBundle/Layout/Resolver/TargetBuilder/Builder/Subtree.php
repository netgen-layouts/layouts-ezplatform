<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LocationService;
use Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\Subtree as SubtreeTarget;
use Symfony\Component\HttpFoundation\Request;

class Subtree implements TargetBuilderInterface
{
    use RequestStackAwareTrait;

    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    protected $locationService;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

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

        try {
            $location = $this->locationService->loadLocation(
                $currentRequest->attributes->get('locationId')
            );
        } catch (NotFoundException $e) {
            return false;
        }

        return new SubtreeTarget(
            $location->path
        );
    }
}
