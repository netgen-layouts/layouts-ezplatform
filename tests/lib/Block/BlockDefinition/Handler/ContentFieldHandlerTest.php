<?php

namespace Netgen\BlockManager\Ez\Tests\Block\BlockDefinition\Handler;

use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use PHPUnit\Framework\TestCase;

class ContentFieldHandlerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentProviderMock;

    /**
     * @var \Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->contentProviderMock = $this->createMock(ContentProviderInterface::class);

        $this->handler = new ContentFieldHandler($this->contentProviderMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler::isContextual
     */
    public function testIsContextual()
    {
        $this->assertTrue($this->handler->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler::__construct
     * @covers \Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler::getDynamicParameters
     */
    public function testGetDynamicParameters()
    {
        $this->contentProviderMock
            ->expects($this->once())
            ->method('provideContent')
            ->will($this->returnValue(new Content()));

        $this->contentProviderMock
            ->expects($this->once())
            ->method('provideLocation')
            ->will($this->returnValue(new Location()));

        $dynamicParameters = $this->handler->getDynamicParameters(new Block());

        $this->assertInternalType('array', $dynamicParameters);

        $this->assertArrayHasKey('content', $dynamicParameters);
        $this->assertArrayHasKey('location', $dynamicParameters);

        $this->assertEquals(new Content(), $dynamicParameters['content']);
        $this->assertEquals(new Location(), $dynamicParameters['location']);
    }
}
