<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Parameters\ValueObjectProvider;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Error\ErrorHandlerInterface;
use Netgen\Layouts\Ez\Parameters\ValueObjectProvider\LocationProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LocationProviderTest extends TestCase
{
    private MockObject $repositoryMock;

    private MockObject $locationServiceMock;

    private LocationProvider $valueObjectProvider;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(Repository::class);
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->repositoryMock
            ->method('getLocationService')
            ->willReturn($this->locationServiceMock);

        $this->repositoryMock
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->valueObjectProvider = new LocationProvider(
            $this->repositoryMock,
            $this->createMock(ErrorHandlerInterface::class),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ValueObjectProvider\LocationProvider::__construct
     * @covers \Netgen\Layouts\Ez\Parameters\ValueObjectProvider\LocationProvider::getValueObject
     */
    public function testGetValueObject(): void
    {
        $location = new Location();

        $this->locationServiceMock
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn($location);

        self::assertSame($location, $this->valueObjectProvider->getValueObject(42));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ValueObjectProvider\LocationProvider::getValueObject
     */
    public function testGetValueObjectWithNullValue(): void
    {
        $this->locationServiceMock
            ->expects(self::never())
            ->method('loadLocation');

        self::assertNull($this->valueObjectProvider->getValueObject(null));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ValueObjectProvider\LocationProvider::getValueObject
     */
    public function testGetValueObjectWithNonExistentLocation(): void
    {
        $this->locationServiceMock
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('location', 42));

        self::assertNull($this->valueObjectProvider->getValueObject(42));
    }
}
