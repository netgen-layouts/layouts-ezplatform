<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Controller\Admin;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Ibexa\AdminUI\RelatedLayoutsLoader;
use Netgen\Layouts\Layout\Resolver\LayoutResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LocationLayouts extends Controller
{
    public function __construct(
        private ContentService $contentService,
        private LayoutResolverInterface $layoutResolver,
        private RelatedLayoutsLoader $relatedLayoutsLoader,
    ) {
    }

    /**
     * Renders a template that shows all layouts applied to provided location.
     */
    public function __invoke(Location $location): Response
    {
        $request = $this->createRequest($location);

        $rules = $this->layoutResolver->resolveRules($request, ['ibexa_content_type']);
        $rulesOneOnOne = [];

        foreach ($rules as $rule) {
            $rulesOneOnOne[$rule->getId()->toString()] = $this->isRuleOneOnOne($location, $rule);
        }

        return $this->render(
            '@ibexadesign/content/tab/nglayouts/location_layouts.html.twig',
            [
                'rules' => $rules,
                'rules_one_on_one' => $rulesOneOnOne,
                'related_layouts' => $this->relatedLayoutsLoader->loadRelatedLayouts($location),
                'location' => $location,
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

    /**
     * Creates the request used for fetching the mappings applied to provided content and location.
     */
    private function createRequest(Location $location): Request
    {
        $request = Request::create('');

        $contentView = new ContentView();
        $contentView->setLocation($location);
        $contentView->setContent(
            $this->contentService->loadContent($location->contentInfo->id),
        );

        $request->attributes->set('view', $contentView);

        return $request;
    }

    /**
     * Returns if the provided rule has a 1:1 mapping to provided location.
     */
    private function isRuleOneOnOne(Location $location, Rule $rule): bool
    {
        if ($rule->getTargets()->count() !== 1) {
            return false;
        }

        /** @var \Netgen\Layouts\API\Values\LayoutResolver\Target $target */
        $target = $rule->getTargets()[0];

        if ($target->getTargetType()::getType() === 'ibexa_location') {
            if ((int) $target->getValue() === (int) $location->id) {
                return true;
            }
        }

        if ($target->getTargetType()::getType() === 'ibexa_content') {
            if ((int) $target->getValue() === (int) $location->contentId) {
                return true;
            }
        }

        return false;
    }
}
