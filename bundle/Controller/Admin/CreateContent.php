<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Controller\Admin;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Netgen\Layouts\API\Values\Block\Block;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateContent extends Controller
{
    public function __construct(
        private LocationService $locationService,
        private ContentTypeService $contentTypeService,
    ) {
    }

    /**
     * Creates a content and redirects to route that edits the content.
     */
    public function __invoke(Request $request, Block $block, string $contentTypeIdentifier, string $languageCode, int $parentLocationId): Response
    {
        $location = $this->locationService->loadLocation($parentLocationId);
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);

        return $this->redirectToRoute(
            'ibexa.content.create.proxy',
            [
                'contentTypeIdentifier' => $contentType->identifier,
                'languageCode' => $languageCode,
                'parentLocationId' => $location->id,
                '_fragment' => 'ngl-component/' . $block->getId()->toString() . '/' . $block->getLocale(),
            ],
        );
    }

    public function checkPermissions(): void
    {
        if ($this->isGranted('ROLE_NGLAYOUTS_EDITOR')) {
            return;
        }

        if ($this->isGranted('nglayouts:ui:access')) {
            return;
        }

        $exception = $this->createAccessDeniedException();
        $exception->setAttributes('nglayouts:ui:access');

        throw $exception;
    }
}
