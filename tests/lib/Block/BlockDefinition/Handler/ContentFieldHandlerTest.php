<?php

declare(strict_types=1);

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

    public function setUp(): void
    {
        $this->contentProviderMock = $this->createMock(ContentProviderInterface::class);

        $this->handler = new ContentFieldHandler($this->contentProviderMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler::isContextual
     */
    public function testIsContextual(): void
    {
        $this->assertTrue($this->handler->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler::__construct
     * @covers \Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler::getDynamicParameters
     */
    public function testGetDynamicParameters(): void
    {
        $content = new Content();
        $location = new Location();

        $this->contentProviderMock
            ->expects($this->once())
            ->method('provideContent')
            ->will($this->returnValue($content));

        $this->contentProviderMock
            ->expects($this->once())
            ->method('provideLocation')
            ->will($this->returnValue($location));

        $params = new DynamicParameters();

        $this->handler->getDynamicParameters($params, new Block());

        $this->assertArrayHasKey('content', $params);
        $this->assertArrayHasKey('location', $params);

        $this->assertSame($content, $params['content']);
        $this->assertSame($location, $params['location']);
    }
}
