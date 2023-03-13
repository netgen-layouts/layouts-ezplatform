<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Repository;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location as IbexaLocation;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Netgen\Layouts\Ibexa\Validator\Constraint\Location;
use Netgen\Layouts\Ibexa\Validator\LocationValidator;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class LocationValidatorTest extends ValidatorTestCase
{
    private MockObject&Repository $repositoryMock;

    private MockObject&LocationService $locationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->constraint = new Location(['allowedTypes' => ['user']]);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\LocationValidator::__construct
     * @covers \Netgen\Layouts\Ibexa\Validator\LocationValidator::validate
     */
    public function testValidateValid(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn(
                new IbexaLocation(
                    [
                        'id' => 42,
                        'content' => new Content(['contentType' => new ContentType(['identifier' => 'user'])]),
                    ],
                ),
            );

        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\LocationValidator::__construct
     * @covers \Netgen\Layouts\Ibexa\Validator\LocationValidator::validate
     */
    public function testValidateInvalidWithWrongType(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn(
                new IbexaLocation(
                    [
                        'id' => 42,
                        'content' => new Content(['contentType' => new ContentType(['identifier' => 'article'])]),
                    ],
                ),
            );

        $this->assertValid(false, 42);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\LocationValidator::__construct
     * @covers \Netgen\Layouts\Ibexa\Validator\LocationValidator::validate
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
     * @covers \Netgen\Layouts\Ibexa\Validator\LocationValidator::__construct
     * @covers \Netgen\Layouts\Ibexa\Validator\LocationValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->locationServiceMock
            ->expects(self::never())
            ->method('loadLocation');

        $this->assertValid(true, null);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\LocationValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\Layouts\\Ibexa\\Validator\\Constraint\\Location", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\LocationValidator::validate
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
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );
        $this->repositoryMock
            ->expects(self::any())
            ->method('getLocationService')
            ->willReturn($this->locationServiceMock);

        return new LocationValidator($this->repositoryMock);
    }
}
