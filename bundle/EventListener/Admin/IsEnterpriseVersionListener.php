<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\EventListener\Admin;

use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Netgen\Layouts\View\View\RuleViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class IsEnterpriseVersionListener implements EventSubscriberInterface
{
    public function __construct(private bool $isEnterpriseVersion) {}

    public static function getSubscribedEvents(): array
    {
        return [LayoutsEvents::BUILD_VIEW => 'onBuildView'];
    }

    /**
     * Injects if Netgen Layouts is the enterprise version or not.
     */
    public function onBuildView(CollectViewParametersEvent $event): void
    {
        $view = $event->getView();
        if (!$view instanceof LayoutViewInterface && !$view instanceof RuleViewInterface) {
            return;
        }

        if ($view->getContext() !== 'ibexa_admin') {
            return;
        }

        $event->addParameter('is_enterprise', $this->isEnterpriseVersion);
    }
}
