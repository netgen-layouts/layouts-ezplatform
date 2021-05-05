<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\EventListener\Admin;

use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\HttpCache\ClientInterface;
use Netgen\Layouts\HttpCache\NullClient;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Netgen\Layouts\View\View\RuleViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CacheEnabledListener implements EventSubscriberInterface
{
    private ClientInterface $httpCacheClient;

    public function __construct(ClientInterface $httpCacheClient)
    {
        $this->httpCacheClient = $httpCacheClient;
    }

    public static function getSubscribedEvents(): array
    {
        return [LayoutsEvents::BUILD_VIEW => 'onBuildView'];
    }

    /**
     * Injects if the HTTP cache clearing is enabled or not.
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
            'http_cache_enabled',
            !$this->httpCacheClient instanceof NullClient,
        );
    }
}
