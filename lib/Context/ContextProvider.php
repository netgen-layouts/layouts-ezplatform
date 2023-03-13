<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Context;

use Ibexa\Contracts\Core\Repository\ContentService;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Context\ContextProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use function in_array;

final class ContextProvider implements ContextProviderInterface
{
    /**
     * @param string[] $allowedRoutes
     */
    public function __construct(
        private RequestStack $requestStack,
        private ContentService $contentService,
        private array $allowedRoutes,
    ) {
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

        $context->set('ibexa_location_id', (int) $currentLocationId);
    }
}
