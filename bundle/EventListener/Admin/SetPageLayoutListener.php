<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\EventListener\Admin;

use EzSystems\EzPlatformAdminUiBundle\EzPlatformAdminUiBundle;
use Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent;
use Netgen\Bundle\LayoutsAdminBundle\Event\LayoutsAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use function in_array;

final class SetPageLayoutListener implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    /**
     * @var array<string, string[]>
     */
    private array $groupsBySiteAccess;

    private string $pageLayoutTemplate;

    /**
     * @param array<string, string[]> $groupsBySiteAccess
     */
    public function __construct(
        RequestStack $requestStack,
        array $groupsBySiteAccess,
        string $pageLayoutTemplate
    ) {
        $this->requestStack = $requestStack;
        $this->groupsBySiteAccess = $groupsBySiteAccess;
        $this->pageLayoutTemplate = $pageLayoutTemplate;
    }

    public static function getSubscribedEvents(): array
    {
        return [LayoutsAdminEvents::ADMIN_MATCH => ['onAdminMatch', -255]];
    }

    /**
     * Sets the pagelayout template for admin interface.
     */
    public function onAdminMatch(AdminMatchEvent $event): void
    {
        $pageLayoutTemplate = $event->getPageLayoutTemplate();
        if ($pageLayoutTemplate !== null) {
            return;
        }

        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        $siteAccess = $currentRequest->attributes->get('siteaccess')->name;
        if (!in_array(EzPlatformAdminUiBundle::ADMIN_GROUP_NAME, $this->groupsBySiteAccess[$siteAccess] ?? [], true)) {
            return;
        }

        $event->setPageLayoutTemplate($this->pageLayoutTemplate);
    }
}
