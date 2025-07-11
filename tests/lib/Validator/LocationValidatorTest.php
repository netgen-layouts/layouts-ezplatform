<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location as EzLocation;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\Layouts\Ez\Validator\Constraint\Location;
use Netgen\Layouts\Ez\Validator\LocationValidator;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class LocationValidatorTest extends ValidatorTestCase
{
    private MockObject $repositoryMock;

    private MockObject $locationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->constraint = new Location(['allowedTypes' => ['user']]);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\LocationValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\LocationValidator::validate
     */
    public function testValidateValid(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn(
                new EzLocation(
                    [
                        'id' => 42,
                        'content' => new Content(['contentType' => new ContentType(['identifier' => 'user'])]),
                    ],
                ),
            );

        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\LocationValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\LocationValidator::validate
     */
    public function testValidateInvalidWithWrongType(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn(
                new EzLocation(
                    [
                        'id' => 42,
                        'content' => new Content(['contentType' => new ContentType(['identifier' => 'article'])]),
                    ],
                ),
            );

        $this->assertValid(false, 42);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\LocationValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\LocationValidator::validate
     */
    public function testValidateInvalidWithNonExistingLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('location', 42));

        $this->assertValid(false, 42);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\LocationValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\LocationValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->locationServiceMock
            ->expects(self::never())
            ->method('loadLocation');

        $this->assertValid(true, null);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\LocationValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Ez\Validator\Constraint\Location", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\LocationValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "scalar", "array" given');

        $this->assertValid(true, []);
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getLocationService']);

        $this->repositoryMock
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );
        $this->repositoryMock
            ->method('getLocationService')
            ->willReturn($this->locationServiceMock);

        return new LocationValidator($this->repositoryMock);
    }
}
