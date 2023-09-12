<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Controller\Admin;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteRule extends Controller
{
    private LayoutService $layoutService;

    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutService $layoutService, LayoutResolverService $layoutResolverService)
    {
        $this->layoutService = $layoutService;
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Deletes the provided rule.
     */
    public function __invoke(Rule $rule, Request $request): Response
    {
        if (!$this->isGranted('ROLE_NGLAYOUTS_ADMIN')) {
            $this->denyAccessUnlessGranted(
                'nglayouts:mapping:delete',
                ['ruleGroup', $rule->getRuleGroupId()->toString()],
            );
        }

        $layout = $rule->getLayout();

        if (
            $layout !== null
            && $this->layoutResolverService->getRuleCountForLayout($layout) === 1
            && ($this->isGranted('ROLE_NGLAYOUTS_ADMIN') || $this->isGranted('nglayouts:layout:delete'))
        ) {
            $this->layoutService->deleteLayout($layout);
        }

        $this->layoutResolverService->deleteRule($rule);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    public function checkPermissions(): void {}
}
