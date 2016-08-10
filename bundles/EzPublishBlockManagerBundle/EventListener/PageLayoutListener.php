<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\EventListener;

use eZ\Publish\Core\MVC\Symfony\Event\ScopeChangeEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;

class PageLayoutListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface
     */
    protected $pageLayoutResolver;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    protected $globalVariable;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface $pageLayoutResolver
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable $globalVariable
     */
    public function __construct(
        PageLayoutResolverInterface $pageLayoutResolver,
        GlobalVariable $globalVariable
    ) {
        $this->pageLayoutResolver = $pageLayoutResolver;
        $this->globalVariable = $globalVariable;
    }

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
        $this->globalVariable->setPageLayoutTemplate(
            $this->pageLayoutResolver->resolvePageLayout()
        );
    }
}
