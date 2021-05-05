<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\ContentProvider;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Ez\ContentProvider\ContentProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ContentProviderTest extends TestCase
{
    private MockObject $locationServiceMock;

    private Context $context;

    private ContentProvider $contentProvider;

    protected function setUp(): void
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->context = new Context();

        $this->contentProvider = new ContentProvider(
            $this->locationServiceMock,
            $this->context,
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\ContentProvider\ContentProvider::__construct
     * @covers \Netgen\Layouts\Ez\ContentProvider\ContentProvider::loadLocation
     * @covers \Netgen\Layouts\Ez\ContentProvider\ContentProvider::provideContent
     */
    public function testProvideContent(): void
    {
        $content = new Content();
        $location = new Location(
            [
                'content' => $content,
            ],
        );

        $this->context->set('ez_location_id', 42);

        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn($location);

        self::assertSame($content, $this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\Layouts\Ez\ContentProvider\ContentProvider::loadLocation
     * @covers \Netgen\Layouts\Ez\ContentProvider\ContentProvider::provideContent
     */
    public function testProvideContentWithoutContent(): void
    {
        $this->locationServiceMock
            ->expects(self::never())
            ->method('loadLocation');

        self::assertNull($this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\Layouts\Ez\ContentProvider\ContentProvider::loadLocation
     * @covers \Netgen\Layouts\Ez\ContentProvider\ContentProvider::provideLocation
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
     * @covers \Netgen\Layouts\Ez\ContentProvider\ContentProvider::loadLocation
     * @covers \Netgen\Layouts\Ez\ContentProvider\ContentProvider::provideLocation
     */
    public function testProvideLocationWithoutLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::never())
            ->method('loadLocation');

        self::assertNull($this->contentProvider->provideLocation());
    }
}
