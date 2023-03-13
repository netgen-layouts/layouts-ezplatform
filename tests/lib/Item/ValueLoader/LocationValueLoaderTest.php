<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Item\ValueLoader;

use Exception;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Ibexa\Item\ValueLoader\LocationValueLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(LocationValueLoader::class)]
final class LocationValueLoaderTest extends TestCase
{
    private MockObject&LocationService $locationServiceMock;

    private LocationValueLoader $valueLoader;

    protected function setUp(): void
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->valueLoader = new LocationValueLoader($this->locationServiceMock);
    }

    public function testLoad(): void
    {
        $location = new Location(
            [
                'id' => 52,
                'contentInfo' => new ContentInfo(
                    [
                        'published' => true,
                    ],
                ),
            ],
        );

        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocation')
            ->with(self::identicalTo(52))
            ->willReturn($location);

        self::assertSame($location, $this->valueLoader->load(52));
    }

    public function testLoadWithNoLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocation')
            ->with(self::identicalTo(52))
            ->willThrowException(new Exception());

        self::assertNull($this->valueLoader->load(52));
    }

    public function testLoadWithNonPublishedContent(): void
    {
        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocation')
            ->with(self::identicalTo(52))
            ->willReturn(
                new Location(
                    [
                        'contentInfo' => new ContentInfo(
                            [
                                'published' => false,
                            ],
                        ),
                    ],
                ),
            );

        self::assertNull($this->valueLoader->load(52));
    }

    public function testLoadByRemoteId(): void
    {
        $location = new Location(
            [
                'remoteId' => 'abc',
                'contentInfo' => new ContentInfo(
                    [
                        'published' => true,
                    ],
                ),
            ],
        );

        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn($location);

        self::assertSame($location, $this->valueLoader->loadByRemoteId('abc'));
    }

    public function testLoadByRemoteIdWithNoLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willThrowException(new Exception());

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }

    public function testLoadByRemoteIdWithNonPublishedContent(): void
    {
        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn(
                new Location(
                    [
                        'contentInfo' => new ContentInfo(
                            [
                                'published' => false,
                            ],
                        ),
                    ],
                ),
            );

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }
}
