<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\EventListener\BlockView;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition as BlockDefinitionStub;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Ez\Block\BlockDefinition\ContentFieldDefinitionHandlerInterface;
use Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\ViewInterface;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use PHPUnit\Framework\TestCase;

class ContentFieldListenerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $blockDefinition;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentProviderMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener
     */
    protected $listener;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->blockDefinition = new BlockDefinition(
            'def',
            $this->createMock(ContentFieldDefinitionHandlerInterface::class),
            $this->createMock(Configuration::class)
        );

        $this->contentProviderMock = $this->createMock(ContentProviderInterface::class);

        $this->listener = new ContentFieldListener(
            $this->contentProviderMock,
            array(ViewInterface::CONTEXT_DEFAULT)
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
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
        $view = new BlockView(new Block(array('blockDefinition' => $this->blockDefinition)));
        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->contentProviderMock
            ->expects($this->any())
            ->method('provideContent')
            ->will($this->returnValue(new Content()));

        $this->contentProviderMock
            ->expects($this->any())
            ->method('provideLocation')
            ->will($this->returnValue(new Location()));

        $this->listener->onBuildView($event);

        $this->assertEquals(
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
        $view = new BlockView(new Block(array('blockDefinition' => $this->blockDefinition)));
        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->contentProviderMock
            ->expects($this->any())
            ->method('provideContent')
            ->will($this->returnValue(new Content()));

        $this->contentProviderMock
            ->expects($this->any())
            ->method('provideLocation')
            ->will($this->returnValue(null));

        $this->listener->onBuildView($event);

        $this->assertEquals(
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

        $this->assertEquals(array(), $event->getViewParameters());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::onBuildView
     */
    public function testOnBuildViewWithWrongContext()
    {
        $view = new BlockView(new Block(array('blockDefinition' => $this->blockDefinition)));
        $view->setContext(ViewInterface::CONTEXT_API);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        $this->assertEquals(array(), $event->getViewParameters());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\EventListener\BlockView\ContentFieldListener::onBuildView
     */
    public function testOnBuildViewWithNoContentFieldBlock()
    {
        $view = new BlockView(new Block(array('blockDefinition' => new BlockDefinitionStub('def'))));
        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        $this->assertEquals(array(), $event->getViewParameters());
    }
}
