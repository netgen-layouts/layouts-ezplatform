<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Context;

use eZ\Publish\API\Repository\ContentService;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Context\ContextProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use function in_array;

final class ContextProvider implements ContextProviderInterface
{
    private RequestStack $requestStack;

    private ContentService $contentService;

    /**
     * @var string[]
     */
    private array $allowedRoutes;

    /**
     * @param string[] $allowedRoutes
     */
    public function __construct(
        RequestStack $requestStack,
        ContentService $contentService,
        array $allowedRoutes
    ) {
        $this->requestStack = $requestStack;
        $this->contentService = $contentService;
        $this->allowedRoutes = $allowedRoutes;
    }

    public function provideContext(Context $context): void
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        $currentRoute = $currentRequest->attributes->get('_route');
        if (!in_array($currentRoute, $this->allowedRoutes, true)) {
            return;
        }

        $currentLocationId = null;

        if ($currentRequest->attributes->has('locationId')) {
            $currentLocationId = $currentRequest->attributes->get('locationId');
        } elseif ($currentRequest->attributes->has('contentId')) {
            $currentContentId = $currentRequest->attributes->get('contentId');
            if ($currentContentId !== null) {
                $currentLocationId = $this->contentService->loadContentInfo(
                    (int) $currentContentId,
                )->mainLocationId;
            }
        }

        if ($currentLocationId === null) {
            return;
        }

        $context->set('ez_location_id', (int) $currentLocationId);
    }
}
