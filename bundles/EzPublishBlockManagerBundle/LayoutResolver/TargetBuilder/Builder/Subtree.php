<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LocationService;
use Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\BlockManager\LayoutResolver\Target;
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
     * Returns the unique identifier of the target this builder builds.
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return 'subtree';
    }

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

        return new Target(
            $this->getTargetIdentifier(),
            $location->path
        );
    }
}
