<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Utils;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Ez\Utils\RemoteIdConverter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RemoteIdConverterTest extends TestCase
{
    private MockObject $locationServiceMock;

    private MockObject $contentServiceMock;

    private MockObject $repositoryMock;

    private RemoteIdConverter $converter;

    protected function setUp(): void
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getLocationService', 'getContentService']);

        $this->repositoryMock
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->method('getLocationService')
            ->willReturn($this->locationServiceMock);

        $this->repositoryMock
            ->method('getContentService')
            ->willReturn($this->contentServiceMock);

        $this->converter = new RemoteIdConverter(
            $this->repositoryMock,
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\Utils\RemoteIdConverter::__construct
     * @covers \Netgen\Layouts\Ez\Utils\RemoteIdConverter::toLocationId
     */
    public function testToLocationId(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn(new Location(['id' => 42]));

        self::assertSame(42, $this->converter->toLocationId('abc'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Utils\RemoteIdConverter::toLocationId
     */
    public function testToLocationIdWithNonExistentRemoteId(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willThrowException(new NotFoundException('location', 'abc'));

        self::assertNull($this->converter->toLocationId('abc'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Utils\RemoteIdConverter::toLocationRemoteId
     */
    public function testToLocationRemoteId(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn(new Location(['remoteId' => 'abc']));

        self::assertSame('abc', $this->converter->toLocationRemoteId(42));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Utils\RemoteIdConverter::toLocationRemoteId
     */
    public function testToLocationRemoteIdWithNonExistentId(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('location', 42));

        self::assertNull($this->converter->toLocationRemoteId(42));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Utils\RemoteIdConverter::toContentId
     */
    public function testToContentId(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfoByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn(new ContentInfo(['id' => 42]));

        self::assertSame(42, $this->converter->toContentId('abc'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Utils\RemoteIdConverter::toContentId
     */
    public function testToContentIdWithNonExistentRemoteId(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfoByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willThrowException(new NotFoundException('content', 'abc'));

        self::assertNull($this->converter->toContentId('abc'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Utils\RemoteIdConverter::toContentRemoteId
     */
    public function testToContentRemoteId(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->willReturn(new ContentInfo(['remoteId' => 'abc']));

        self::assertSame('abc', $this->converter->toContentRemoteId(42));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Utils\RemoteIdConverter::toContentRemoteId
     */
    public function testToContentRemoteIdWithNonExistentId(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('content', 42));

        self::assertNull($this->converter->toContentRemoteId(42));
    }
}
