<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Context;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use Netgen\Layouts\Context\ContextInterface;
use Netgen\Layouts\Context\ContextProviderInterface;
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

    public function provideContext(ContextInterface $context): void
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        $locationId = null;

        // Normally, here we would use request content extractor to
        // extract the location from the request, but since currently
        // it only supports extracting the content from "view" attribute,
        // it cannot be used here since this is executed way before "view"
        // attribute is even populated in the request.

        // @todo Consider refactoring the context builder to allow for some
        // kind of provider fallback.

        if ($currentRequest->attributes->has('view')) {
            $view = $currentRequest->attributes->get('view');
            if ($view instanceof LocationValueView) {
                $locationId = $view->getLocation()->id;
            }
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
