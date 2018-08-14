<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Block\BlockDefinition\Handler;

use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\DynamicParameters;
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
        self::assertTrue($this->handler->isContextual(new Block()));
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
            ->expects(self::once())
            ->method('provideContent')
            ->will(self::returnValue($content));

        $this->contentProviderMock
            ->expects(self::once())
            ->method('provideLocation')
            ->will(self::returnValue($location));

        $params = new DynamicParameters();

        $this->handler->getDynamicParameters($params, new Block());

        self::assertArrayHasKey('content', $params);
        self::assertArrayHasKey('location', $params);

        self::assertSame($content, $params['content']);
        self::assertSame($location, $params['location']);
    }
}
