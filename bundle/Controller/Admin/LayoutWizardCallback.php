<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Controller\Admin;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Location as LocationTargetType;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use function count;
use function max;
use function sprintf;

final class LayoutWizardCallback extends Controller
{
    public function __construct(private LayoutService $layoutService, private LayoutResolverService $layoutResolverService) {}

    /**
     * Creates a new 1:1 mapping based on data located in the session.
     */
    public function __invoke(Location $location, Request $request): RedirectResponse
    {
        $wizardId = sprintf('_layouts_ibexa_wizard/%s', $request->query->get('wizardId', ''));
        if (!$request->getSession()->has($wizardId)) {
            return $this->redirectToRoute(
                UrlAliasGenerator::INTERNAL_CONTENT_VIEW_ROUTE,
                [
                    'contentId' => $location->contentId,
                    'locationId' => $location->id,
                    '_fragment' => 'ibexa-tab-location-view-netgen_layouts',
                ],
            );
        }

        $wizardData = $request->getSession()->get($wizardId);
        $request->getSession()->remove($wizardId);

        $layoutId = Uuid::fromString($wizardData['layout']);

        if (!$this->layoutService->layoutExists($layoutId, Layout::STATUS_PUBLISHED)) {
            return $this->redirectToRoute(
                UrlAliasGenerator::INTERNAL_CONTENT_VIEW_ROUTE,
                [
                    'contentId' => $location->contentId,
                    'locationId' => $location->id,
                    '_fragment' => 'ibexa-tab-location-view-netgen_layouts',
                ],
            );
        }

        $ruleGroupId = $wizardData['rule_group'] ?? RuleGroup::ROOT_UUID;
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString($ruleGroupId));

        $groupRules = $this->layoutResolverService->loadRulesFromGroup($ruleGroup, 0, 1)->getRules();
        $subGroups = $this->layoutResolverService->loadRuleGroups($ruleGroup, 0, 1)->getRuleGroups();

        $priority1 = count($groupRules) > 0 ? $groupRules[0]->getPriority() + 10 : 0;
        $priority2 = count($subGroups) > 0 ? $subGroups[0]->getPriority() + 10 : 0;

        $ruleCreateStruct = $this->layoutResolverService->newRuleCreateStruct();
        $ruleCreateStruct->layoutId = $layoutId;
        $ruleCreateStruct->enabled = (bool) $wizardData['activate_rule'];
        $ruleCreateStruct->priority = max($priority1, $priority2);

        $rule = $this->layoutResolverService->createRule($ruleCreateStruct, $ruleGroup);

        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(LocationTargetType::getType());
        $targetCreateStruct->value = $location->id;

        $this->layoutResolverService->addTarget($rule, $targetCreateStruct);
        $this->layoutResolverService->publishRule($rule);

        return $this->redirectToRoute(
            UrlAliasGenerator::INTERNAL_CONTENT_VIEW_ROUTE,
            [
                'contentId' => $location->contentId,
                'locationId' => $location->id,
                '_fragment' => 'ibexa-tab-location-view-netgen_layouts',
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
