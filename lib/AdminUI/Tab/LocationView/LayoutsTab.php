<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\AdminUI\Tab\LocationView;

use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\PermissionService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class LayoutsTab extends AbstractEventDispatchingTab implements ConditionalTabInterface
{
    private PermissionService $permissionService;

    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        PermissionService $permissionService,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->permissionService = $permissionService;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getIdentifier(): string
    {
        return 'netgen_layouts';
    }

    public function getName(): string
    {
        return 'Netgen Layouts';
    }

    /**
     * @param array<mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        try {
            return $this->permissionService->hasAccess('nglayouts', 'editor') !== false;
        } catch (InvalidArgumentException $e) {
            // If nglayouts/editor permission does not exist (e.g. when using Netgen Layouts Enterprise)
            return $this->authorizationChecker->isGranted('nglayouts:ui:access');
        }
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/content/tab/nglayouts/tab.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        return $contextParameters;
    }
}
