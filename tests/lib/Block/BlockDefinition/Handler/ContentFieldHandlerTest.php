<?php

namespace Netgen\BlockManager\Ez\Tests\Block\BlockDefinition\Handler;

use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use PHPUnit\Framework\TestCase;

final class ContentFieldHandlerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentProviderMock;

    /**
     * @var \Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler
     */
    private $handler;

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

        $params = new DynamicParameters();

        $this->handler->getDynamicParameters($params, new Block());

        $this->assertArrayHasKey('content', $params);
        $this->assertArrayHasKey('location', $params);

        $this->assertEquals(new Content(), $params['content']);
        $this->assertEquals(new Location(), $params['location']);
    }
}
