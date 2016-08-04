<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\TargetType;

use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LocationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class Subtree implements TargetTypeInterface
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
     * Returns the target type.
     *
     * @return string
     */
    public function getType()
    {
        return 'ezsubtree';
    }

    /**
     * Returns the constraints that will be used to validate the target value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Type(array('type' => 'numeric')),
            new Constraints\GreaterThan(array('value' => 0)),
            new EzConstraints\Location(),
        );
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

        $attributes = $currentRequest->attributes;
        if ($attributes->get('_route') !== UrlAliasRouter::URL_ALIAS_ROUTE_NAME) {
            return;
        }

        if (!$attributes->has('locationId')) {
            return;
        }

        try {
            $location = $this->locationService->loadLocation(
                $attributes->get('locationId')
            );
        } catch (NotFoundException $e) {
            return;
        }

        return $location->path;
    }
}
