<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Controller\Admin;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Ez\Layout\Resolver\TargetType\Location as LocationTargetType;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use function count;
use function max;
use function sprintf;

final class LayoutWizardCallback extends Controller
{
    private LayoutService $layoutService;

    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutService $layoutService, LayoutResolverService $layoutResolverService)
    {
        $this->layoutService = $layoutService;
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Creates a new 1:1 mapping based on data located in the session.
     */
    public function __invoke(Location $location, Request $request): RedirectResponse
    {
        $wizardId = sprintf('_layouts_ezplatform_wizard/%s', (string) $request->query->get('wizardId', ''));
        if (!$request->getSession()->has($wizardId)) {
            return $this->redirectToRoute(
                UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
                [
                    'locationId' => $location->id,
                    '_fragment' => 'ez-tab-location-view-netgen_layouts',
                ],
            );
        }

        $wizardData = $request->getSession()->get($wizardId);
        $request->getSession()->remove($wizardId);

        $layoutId = Uuid::fromString($wizardData['layout']);

        if (!$this->layoutService->layoutExists($layoutId, Layout::STATUS_PUBLISHED)) {
            return $this->redirectToRoute(
                UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
                [
                    'locationId' => $location->id,
                    '_fragment' => 'ez-tab-location-view-netgen_layouts',
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
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            [
                'locationId' => $location->id,
                '_fragment' => 'ez-tab-location-view-netgen_layouts',
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
