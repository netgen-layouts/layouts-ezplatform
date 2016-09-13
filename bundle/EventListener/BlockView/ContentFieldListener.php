<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\Ez\Block\BlockDefinition\ContentFieldDefinitionHandlerInterface;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Event\View\ViewEvents;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentFieldListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface
     */
    protected $contentProvider;

    /**
     * @var array
     */
    protected $enabledContexts;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface $contentProvider
     * @param array $enabledContexts
     */
    public function __construct(ContentProviderInterface $contentProvider, array $enabledContexts = array())
    {
        $this->contentProvider = $contentProvider;
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

        $content = $this->contentProvider->provideContent();
        $location = $this->contentProvider->provideLocation();

        if ($content instanceof Content) {
            $event->getParameterBag()->set('content', $content);
            if ($location instanceof Location) {
                $event->getParameterBag()->set('location', $location);
            }
        }
    }
}
