<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\Ez\Block\BlockDefinition\ContentFieldDefinitionHandlerInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Event\View\ViewEvents;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

class ContentFieldListener implements EventSubscriberInterface
{
    use RequestStackAwareTrait;

    /**
     * @var array
     */
    protected $enabledContexts;

    /**
     * Constructor.
     *
     * @param array $enabledContexts
     */
    public function __construct(array $enabledContexts = array())
    {
        $this->enabledContexts = $enabledContexts;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(ViewEvents::BUILD_VIEW => 'onBuildView');
    }

    /**
     * Includes the content and location into eZ content field block view if specified.
     *
     * @param \Netgen\BlockManager\Event\View\CollectViewParametersEvent $event
     */
    public function onBuildView(CollectViewParametersEvent $event)
    {
        $view = $event->getView();
        if (!$view instanceof BlockViewInterface) {
            return;
        }

        if (!in_array($view->getContext(), $this->enabledContexts)) {
            return;
        }

        $blockDefinition = $view->getBlock()->getBlockDefinition();
        if (!$blockDefinition->getHandler() instanceof ContentFieldDefinitionHandlerInterface) {
            return;
        }

        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        $view = $currentRequest->attributes->get('view');
        if ($view instanceof ContentValueView) {
            $content = $view->getContent();
            $location = $view->getLocation();
        } else {
            // @deprecated BC for eZ Publish 5
            $content = $currentRequest->attributes->get('content');
            $location = $currentRequest->attributes->get('location');
        }

        if ($content instanceof Content) {
            $event->getParameterBag()->set('content', $content);
            if ($location instanceof Location) {
                $event->getParameterBag()->set('location', $location);
            }
        }
    }
}
