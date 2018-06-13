<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Netgen\BlockManager\Ez\Validator\Constraint\Location;
use Netgen\BlockManager\Ez\Validator\LocationValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class LocationValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $locationServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new Location();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getLocationService']);

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function (callable $callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getLocationService')
            ->will($this->returnValue($this->locationServiceMock));

        return new LocationValidator($this->repositoryMock);
    }

    /**
     * @param int|string|null $locationId
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($locationId, bool $isValid): void
    {
        if ($locationId !== null) {
            $this->locationServiceMock
                ->expects($this->once())
                ->method('loadLocation')
                ->with($this->equalTo($locationId))
                ->will(
                    $this->returnCallback(
                        function () use ($locationId): void {
                            if (!is_int($locationId) || $locationId <= 0 || $locationId > 20) {
                                throw new NotFoundException('location', $locationId);
                            }
                        }
                    )
                );
        }

        $this->assertValid($isValid, $locationId);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\Location", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "scalar", "array" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->assertValid(true, []);
    }

    public function validateDataProvider(): array
    {
        return [
            [12, true],
            [25, false],
            [-12, false],
            [0, false],
            ['12', false],
            [null, true],
        ];
    }
}
