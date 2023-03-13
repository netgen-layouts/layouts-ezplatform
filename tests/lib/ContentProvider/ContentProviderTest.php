<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\ContentProvider;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Ibexa\ContentProvider\ContentProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContentProvider::class)]
final class ContentProviderTest extends TestCase
{
    private MockObject&LocationService $locationServiceMock;

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

    public function testProvideContent(): void
    {
        $content = new Content();
        $location = new Location(
            [
                'content' => $content,
            ],
        );

        $this->context->set('ibexa_location_id', 42);

        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn($location);

        self::assertSame($content, $this->contentProvider->provideContent());
    }

    public function testProvideContentWithoutContent(): void
    {
        $this->locationServiceMock
            ->expects(self::never())
            ->method('loadLocation');

        self::assertNull($this->contentProvider->provideContent());
    }

    public function testProvideLocation(): void
    {
        $location = new Location();

        $this->context->set('ibexa_location_id', 42);

        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn($location);

        self::assertSame($location, $this->contentProvider->provideLocation());
    }

    public function testProvideLocationWithoutLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::never())
            ->method('loadLocation');

        self::assertNull($this->contentProvider->provideLocation());
    }
}
