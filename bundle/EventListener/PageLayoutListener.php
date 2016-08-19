<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\PageLayoutListener as BasePageLayoutListener;
use eZ\Publish\Core\MVC\Symfony\Event\ScopeChangeEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;

class PageLayoutListener extends BasePageLayoutListener
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            MVCEvents::CONFIG_SCOPE_CHANGE => 'onScopeChange',
            MVCEvents::CONFIG_SCOPE_RESTORE => 'onScopeChange',
        );
    }

    /**
     * Resolves the main page layout to be used when the scope changes.
     *
     * @param \eZ\Publish\Core\MVC\Symfony\Event\ScopeChangeEvent $event
     */
    public function onScopeChange(ScopeChangeEvent $event)
    {
        $this->resolvePageLayout();
    }
}
