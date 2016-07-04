<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\BlockView;

use Netgen\BlockManager\Ez\Block\BlockDefinition\ContentFieldDefinitionHandlerInterface;
use Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\ViewInterface;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use PHPUnit\Framework\TestCase;

class ContentFieldListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener
     */
    protected $listener;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->listener = new ContentFieldListener(
            array(ViewInterface::CONTEXT_VIEW)
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            array(ViewEvents::BUILD_VIEW => 'onBuildView'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::onBuildView
     */
    public function testOnBuildView()
    {
        $blockDefinition = new BlockDefinition(
            'def',
            $this->createMock(ContentFieldDefinitionHandlerInterface::class),
            $this->createMock(Configuration::class)
        );

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_VIEW);
        $event = new CollectViewParametersEvent($view);

        $contentView = new ContentView();
        $contentView->setContent(new Content());
        $contentView->setLocation(new Location());

        $request = Request::create('/');
        $request->attributes->set('view', $contentView);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $this->listener->setRequestStack($requestStack);
        $this->listener->onBuildView($event);

        self::assertEquals(
            array(
                'content' => new Content(),
                'location' => new Location(),
            ),
            $event->getViewParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::onBuildView
     */
    public function testOnBuildViewWithNoLocation()
    {
        $blockDefinition = new BlockDefinition(
            'def',
            $this->createMock(ContentFieldDefinitionHandlerInterface::class),
            $this->createMock(Configuration::class)
        );

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_VIEW);
        $event = new CollectViewParametersEvent($view);

        $contentView = new ContentView();
        $contentView->setContent(new Content());

        $request = Request::create('/');
        $request->attributes->set('view', $contentView);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $this->listener->setRequestStack($requestStack);
        $this->listener->onBuildView($event);

        self::assertEquals(
            array(
                'content' => new Content(),
            ),
            $event->getViewParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::onBuildView
     */
    public function testOnBuildViewWithNoContentViewAndLegacyFallback()
    {
        $blockDefinition = new BlockDefinition(
            'def',
            $this->createMock(ContentFieldDefinitionHandlerInterface::class),
            $this->createMock(Configuration::class)
        );

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_VIEW);
        $event = new CollectViewParametersEvent($view);

        $request = Request::create('/');
        $request->attributes->set('content', new Content());
        $request->attributes->set('location', new Location());
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $this->listener->setRequestStack($requestStack);
        $this->listener->onBuildView($event);

        self::assertEquals(
            array(
                'content' => new Content(),
                'location' => new Location(),
            ),
            $event->getViewParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::onBuildView
     */
    public function testOnBuildViewWithNoContentViewAndLegacyFallbackAndNoLocation()
    {
        $blockDefinition = new BlockDefinition(
            'def',
            $this->createMock(ContentFieldDefinitionHandlerInterface::class),
            $this->createMock(Configuration::class)
        );

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_VIEW);
        $event = new CollectViewParametersEvent($view);

        $request = Request::create('/');
        $request->attributes->set('content', new Content());
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $this->listener->setRequestStack($requestStack);
        $this->listener->onBuildView($event);

        self::assertEquals(
            array(
                'content' => new Content(),
            ),
            $event->getViewParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::onBuildView
     */
    public function testOnBuildViewWithNoBlockView()
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        self::assertEquals(array(), $event->getViewParameters());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::onBuildView
     */
    public function testOnBuildViewWithWrongContext()
    {
        $blockDefinition = new BlockDefinition(
            'def',
            $this->createMock(ContentFieldDefinitionHandlerInterface::class),
            $this->createMock(Configuration::class)
        );

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_API_VIEW);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        self::assertEquals(array(), $event->getViewParameters());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::onBuildView
     */
    public function testOnBuildViewWithNoContentFieldBlock()
    {
        $blockDefinition = new BlockDefinition(
            'def',
            $this->createMock(BlockDefinitionHandlerInterface::class),
            $this->createMock(Configuration::class)
        );

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_VIEW);
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        self::assertEquals(array(), $event->getViewParameters());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::onBuildView
     */
    public function testOnBuildViewWithNoRequest()
    {
        $blockDefinition = new BlockDefinition(
            'def',
            $this->createMock(ContentFieldDefinitionHandlerInterface::class),
            $this->createMock(Configuration::class)
        );

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_VIEW);
        $event = new CollectViewParametersEvent($view);

        $requestStack = new RequestStack();

        $this->listener->setRequestStack($requestStack);
        $this->listener->onBuildView($event);

        self::assertEquals(array(), $event->getViewParameters());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::onBuildView
     */
    public function testOnBuildViewWithNoContentView()
    {
        $blockDefinition = new BlockDefinition(
            'def',
            $this->createMock(ContentFieldDefinitionHandlerInterface::class),
            $this->createMock(Configuration::class)
        );

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_VIEW);
        $event = new CollectViewParametersEvent($view);

        $request = Request::create('/');
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $this->listener->setRequestStack($requestStack);
        $this->listener->onBuildView($event);

        self::assertEquals(array(), $event->getViewParameters());
    }
}
