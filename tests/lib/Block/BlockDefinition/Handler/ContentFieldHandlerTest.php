<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Block\BlockDefinition\Handler;

use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Ez\Block\BlockDefinition\Handler\ContentFieldHandler;
use Netgen\Layouts\Ez\ContentProvider\ContentProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ContentFieldHandlerTest extends TestCase
{
    private MockObject $contentProviderMock;

    private ContentFieldHandler $handler;

    protected function setUp(): void
    {
        $this->contentProviderMock = $this->createMock(ContentProviderInterface::class);

        $this->handler = new ContentFieldHandler($this->contentProviderMock);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Block\BlockDefinition\Handler\ContentFieldHandler::isContextual
     */
    public function testIsContextual(): void
    {
        self::assertTrue($this->handler->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Block\BlockDefinition\Handler\ContentFieldHandler::__construct
     * @covers \Netgen\Layouts\Ez\Block\BlockDefinition\Handler\ContentFieldHandler::getDynamicParameters
     */
    public function testGetDynamicParameters(): void
    {
        $content = new Content();
        $location = new Location();

        $this->contentProviderMock
            ->expects(self::once())
            ->method('provideContent')
            ->willReturn($content);

        $this->contentProviderMock
            ->expects(self::once())
            ->method('provideLocation')
            ->willReturn($location);

        $params = new DynamicParameters();

        $this->handler->getDynamicParameters($params, new Block());

        self::assertTrue($params->offsetExists('content'));
        self::assertTrue($params->offsetExists('location'));

        self::assertSame($content, $params['content']);
        self::assertSame($location, $params['location']);
    }
}
