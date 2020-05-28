<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\AdminUI\Tab\LocationView;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

final class LayoutsTab extends AbstractEventDispatchingTab implements ConditionalTabInterface
{
    /**
     * @var \eZ\Publish\API\Repository\PermissionResolver
     */
    private $permissionResolver;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(
        Environment $twig,
        $translator,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
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
            return $this->permissionResolver->hasAccess('nglayouts', 'editor') !== false;
        } catch (InvalidArgumentException $e) {
            // If nglayouts/editor permission does not exist (e.g. when using Netgen Layouts Enterprise)
            return $this->authorizationChecker->isGranted('nglayouts:ui:access');
        }
    }

    public function getTemplate(): string
    {
        return '@ezdesign/content/tab/nglayouts/tab.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        return $contextParameters;
    }
}
