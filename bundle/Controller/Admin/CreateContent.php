<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Controller\Admin;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use Netgen\Layouts\API\Values\Block\Block;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateContent extends Controller
{
    private LocationService $locationService;

    private ContentTypeService $contentTypeService;

    public function __construct(
        LocationService $locationService,
        ContentTypeService $contentTypeService
    ) {
        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * Creates a content and redirects to route that edits the content.
     */
    public function __invoke(Request $request, Block $block, string $contentTypeIdentifier, string $languageCode, int $parentLocationId): Response
    {
        $location = $this->locationService->loadLocation($parentLocationId);
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);

        return $this->redirectToRoute(
            'ez_content_create_no_draft',
            [
                'contentTypeIdentifier' => $contentType->identifier,
                'language' => $languageCode,
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
