<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LocationService;
use Netgen\BlockManager\Layout\Resolver\TargetValueProviderInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class Children implements TargetValueProviderInterface
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

        try {
            $location = $this->locationService->loadLocation(
                $currentRequest->attributes->get('locationId')
            );
        } catch (NotFoundException $e) {
            return;
        }

        return $location->parentLocationId;
    }
}
