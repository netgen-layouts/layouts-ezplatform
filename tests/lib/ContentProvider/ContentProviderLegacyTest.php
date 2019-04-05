<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\ContentProvider;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Context\Context;
use Netgen\BlockManager\Ez\ContentProvider\ContentProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

final class ContentProviderLegacyTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $locationServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentServiceMock;

    /**
     * @var \Netgen\BlockManager\Context\ContextInterface
     */
    private $context;

    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\ContentProvider
     */
    private $contentProvider;

    public function setUp(): void
    {
        if (Kernel::VERSION_ID >= 30000) {
            self::markTestSkipped('This test requires eZ Publish kernel 6.13 to run.');
        }

        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->context = new Context();

        $this->contentProvider = new ContentProvider(
            $this->locationServiceMock,
            $this->contentServiceMock,
            $this->context
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::__construct
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::loadLocation
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::provideContent
     */
    public function testProvideContent(): void
    {
        $content = new Content();
        $location = new Location(
            [
                'contentInfo' => new ContentInfo(
                    [
                        'id' => 24,
                    ]
                ),
            ]
        );

        $this->context->set('ez_location_id', 42);

        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn($location);

        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContent')
            ->with(self::identicalTo(24))
            ->willReturn($content);

        self::assertSame($content, $this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::loadLocation
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::provideContent
     */
    public function testProvideContentWithoutContent(): void
    {
        $this->locationServiceMock
            ->expects(self::never())
            ->method('loadLocation');

        $this->contentServiceMock
            ->expects(self::never())
            ->method('loadContent');

        self::assertNull($this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::loadLocation
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::provideLocation
     */
    public function testProvideLocation(): void
    {
        $location = new Location();

        $this->context->set('ez_location_id', 42);

        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn($location);

        self::assertSame($location, $this->contentProvider->provideLocation());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::loadLocation
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::provideLocation
     */
    public function testProvideLocationWithoutLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::never())
            ->method('loadLocation');

        self::assertNull($this->contentProvider->provideLocation());
    }
}
