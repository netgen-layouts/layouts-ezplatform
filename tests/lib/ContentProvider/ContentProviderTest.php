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

final class ContentProviderTest extends TestCase
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
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->identicalTo(42))
            ->will($this->returnValue($location));

        $this->contentServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with($this->identicalTo(24))
            ->will($this->returnValue($content));

        $this->assertSame($content, $this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::loadLocation
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::provideContent
     */
    public function testProvideContentWithoutContent(): void
    {
        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->contentServiceMock
            ->expects($this->never())
            ->method('loadContent');

        $this->assertNull($this->contentProvider->provideContent());
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
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->identicalTo(42))
            ->will($this->returnValue($location));

        $this->assertSame($location, $this->contentProvider->provideLocation());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::loadLocation
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::provideLocation
     */
    public function testProvideLocationWithoutLocation(): void
    {
        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->assertNull($this->contentProvider->provideLocation());
    }
}
