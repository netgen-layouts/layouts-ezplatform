<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Context;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use Netgen\BlockManager\Context\ContextInterface;
use Netgen\BlockManager\Context\ContextProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ContextProvider implements ContextProviderInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function provideContext(ContextInterface $context)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        $locationId = null;
        if ($currentRequest->attributes->has('view')) {
            $view = $currentRequest->attributes->get('view');
            if ($view instanceof LocationValueView) {
                $locationId = $view->getLocation()->id;
            }

            // eZ 5 support
        } elseif ($currentRequest->attributes->has('location')) {
            $location = $currentRequest->attributes->get('location');
            if ($location instanceof Location) {
                $locationId = $location->id;
            }
        } elseif ($currentRequest->attributes->has('locationId')) {
            $currentLocationId = $currentRequest->attributes->get('locationId');
            $currentRoute = $currentRequest->attributes->get('_route');
            if ($currentLocationId !== null && $currentRoute === UrlAliasRouter::URL_ALIAS_ROUTE_NAME) {
                $locationId = $currentLocationId;
            }
        }

        if ($locationId !== null) {
            $context->set('ez_location_id', (int) $locationId);
        }
    }
}
