<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Layout\Resolver\ValueObjectProvider;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Ibexa\Layout\Resolver\ValueObjectProvider\LocationProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LocationProviderTest extends TestCase
{
    private MockObject&Repository $repositoryMock;

    private MockObject&LocationService $locationServiceMock;

    private LocationProvider $valueObjectProvider;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(Repository::class);
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->repositoryMock
            ->expects(self::any())
            ->method('getLocationService')
            ->willReturn($this->locationServiceMock);

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->valueObjectProvider = new LocationProvider(
            $this->repositoryMock,
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ValueObjectProvider\LocationProvider::__construct
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ValueObjectProvider\LocationProvider::getValueObject
     */
    public function testGetValueObject(): void
    {
        $location = new Location();

        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn($location);

        self::assertSame($location, $this->valueObjectProvider->getValueObject(42));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ValueObjectProvider\LocationProvider::getValueObject
     */
    public function testGetValueObjectWithNonExistentLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('location', 42));

        self::assertNull($this->valueObjectProvider->getValueObject(42));
    }
}
