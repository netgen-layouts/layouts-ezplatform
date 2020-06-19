<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Controller\Admin;

use eZ\Publish\API\Repository\ContentService;
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
     * @var \Netgen\Layouts\Layout\Resolver\LayoutResolverInterface
     */
    private $layoutResolver;

    /**
     * @var \Netgen\Layouts\Ez\AdminUI\RelatedLayoutsLoader
     */
    private $relatedLayoutsLoader;

    public function __construct(
        ContentService $contentService,
        LayoutResolverInterface $layoutResolver,
        RelatedLayoutsLoader $relatedLayoutsLoader
    ) {
        $this->contentService = $contentService;
        $this->layoutResolver = $layoutResolver;
        $this->relatedLayoutsLoader = $relatedLayoutsLoader;
    }

    /**
     * Renders a template that shows all layouts applied to provided location.
     */
    public function __invoke(Location $location): Response
    {
        $request = $this->createRequest($location);

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
    private function createRequest(Location $location): Request
    {
        $request = Request::create('');

        $contentView = new ContentView();
        $contentView->setLocation($location);
        $contentView->setContent(
            $this->contentService->loadContent($location->contentInfo->id)
        );

        $request->attributes->set('view', $contentView);

        return $request;
    }
}
