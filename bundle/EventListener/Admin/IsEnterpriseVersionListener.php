<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\EventListener\Admin;

use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Netgen\Layouts\View\View\RuleViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function array_key_exists;

final class IsEnterpriseVersionListener implements EventSubscriberInterface
{
    /**
     * @var array<string, class-string>
     */
    private array $activatedBundles;

    /**
     * @param array<string, class-string> $activatedBundles
     */
    public function __construct(array $activatedBundles)
    {
        $this->activatedBundles = $activatedBundles;
    }

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

        if ($view->getContext() !== 'ezadminui') {
            return;
        }

        $event->addParameter(
            'is_enterprise',
            array_key_exists('NetgenLayoutsEnterpriseBundle', $this->activatedBundles),
        );
    }
}
