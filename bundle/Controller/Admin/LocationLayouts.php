<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Controller\Admin;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Netgen\Layouts\Ez\AdminUI\RelatedLayoutsLoader;
use Netgen\Layouts\Layout\Resolver\LayoutResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LocationLayouts extends Controller
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    /**
     * @var \Netgen\Layouts\Layout\Resolver\LayoutResolverInterface
     */
    private $layoutResolver;

    /**
     * @var \Netgen\Layouts\Ez\AdminUI\RelatedLayoutsLoader
     */
    private $relatedLayoutsLoader;

    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        LayoutResolverInterface $layoutResolver,
        RelatedLayoutsLoader $relatedLayoutsLoader
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->layoutResolver = $layoutResolver;
        $this->relatedLayoutsLoader = $relatedLayoutsLoader;
    }

    /**
     * Renders a template that shows all layouts applied to provided location.
     */
    public function __invoke(int $locationId): Response
    {
        $location = $this->locationService->loadLocation($locationId);
        $content = $this->contentService->loadContent($location->contentInfo->id);

        $request = $this->createRequest($content, $location);

        return $this->render(
            '@ezdesign/content/tab/nglayouts/location_layouts.html.twig',
            [
                'rules' => $this->layoutResolver->resolveRules($request, ['ez_content_type']),
                'related_layouts' => $this->relatedLayoutsLoader->loadRelatedLayouts($location),
                'location' => $location,
            ]
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

    /**
     * Creates the request used for fetching the mappings applied to provided content and location.
     */
    private function createRequest(Content $content, Location $location): Request
    {
        $request = Request::create('');

        $contentView = new ContentView();
        $contentView->setLocation($location);
        $contentView->setContent($content);

        $request->attributes->set('view', $contentView);

        return $request;
    }
}
