<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\Layouts\Ez\Parameters\ParameterType\LocationType;
use Netgen\Layouts\Ez\Tests\Validator\RepositoryValidatorFactory;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ValueObjectProviderInterface;
use Netgen\Layouts\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

use function is_int;

final class LocationTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\eZ\Publish\API\Repository\Repository
     */
    private MockObject $repositoryMock;

    private MockObject $valueObjectProviderMock;

    private MockObject $locationServiceMock;

    protected function setUp(): void
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getLocationService']);
        $this->valueObjectProviderMock = $this->createMock(ValueObjectProviderInterface::class);

        $this->repositoryMock
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->method('getLocationService')
            ->willReturn($this->locationServiceMock);

        $this->type = new LocationType($this->repositoryMock, $this->valueObjectProviderMock);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType::__construct
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('ez_location', $this->type::getIdentifier());
    }

    /**
     * @param array<string, mixed> $options
     * @param array<string, mixed> $resolvedOptions
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType::configureOptions
     *
     * @dataProvider validOptionsDataProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @param array<string, mixed> $options
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType::configureOptions
     *
     * @dataProvider invalidOptionsDataProvider
     */
    public function testInvalidOptions(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->getParameterDefinition($options);
    }

    /**
     * Provider for testing valid parameter attributes.
     */
    public static function validOptionsDataProvider(): iterable
    {
        return [
            [
                [],
                [
                    'allow_invalid' => false,
                    'allowed_types' => [],
                ],
            ],
            [
                [
                    'allow_invalid' => false,
                ],
                [
                    'allow_invalid' => false,
                    'allowed_types' => [],
                ],
            ],
            [
                [
                    'allow_invalid' => true,
                ],
                [
                    'allow_invalid' => true,
                    'allowed_types' => [],
                ],
            ],
            [
                [
                    'allowed_types' => [],
                ],
                [
                    'allow_invalid' => false,
                    'allowed_types' => [],
                ],
            ],
            [
                [
                    'allowed_types' => ['image', 'user'],
                ],
                [
                    'allow_invalid' => false,
                    'allowed_types' => ['image', 'user'],
                ],
            ],
        ];
    }

    /**
     * Provider for testing invalid parameter attributes.
     */
    public static function invalidOptionsDataProvider(): iterable
    {
        return [
            [
                [
                    'allow_invalid' => 'false',
                ],
                [
                    'allow_invalid' => 'true',
                ],
                [
                    'allow_invalid' => 0,
                ],
                [
                    'allow_invalid' => 1,
                ],
                [
                    'allowed_types' => 'image',
                ],
                [
                    'allowed_types' => [42],
                ],
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType::export
     */
    public function testExport(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn(new Location(['remoteId' => 'abc']));

        self::assertSame('abc', $this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType::export
     */
    public function testExportWithNonExistingLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('location', 42));

        self::assertNull($this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType::import
     */
    public function testImport(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn(new Location(['id' => 42]));

        self::assertSame(42, $this->type->import($this->getParameterDefinition(), 'abc'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType::import
     */
    public function testImportWithNonExistingLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willThrowException(new NotFoundException('location', 'abc'));

        self::assertNull($this->type->import($this->getParameterDefinition(), 'abc'));
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType::getValueConstraints
     *
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, string $type, bool $required, bool $isValid): void
    {
        if ($value !== null) {
            $this->locationServiceMock
                ->expects(self::once())
                ->method('loadLocation')
                ->with(self::identicalTo((int) $value))
                ->willReturnCallback(
                    static function () use ($value, $type): Location {
                        if (!is_int($value) || $value <= 0) {
                            throw new NotFoundException('location', $value);
                        }

                        return new Location(
                            [
                                'id' => $value,
                                'content' => new Content(
                                    [
                                        'contentType' => new ContentType(['identifier' => $type]),
                                    ],
                                ),
                            ],
                        );
                    },
                );
        }

        $parameter = $this->getParameterDefinition(['allowed_types' => ['user', 'image']], $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     */
    public static function validationDataProvider(): iterable
    {
        return [
            [12, 'user', false, true],
            [12, 'image', false, true],
            [12, 'article', false, false],
            [-12, 'user', false, false],
            [0, 'user', false, false],
            ['12', 'user', false, false],
            ['', 'user', false, false],
            [null, 'user', false, true],
            [12, 'user', true, true],
            [12, 'image', true, true],
            [12, 'article', true, false],
            [-12, 'user', true, false],
            [0, 'user', true, false],
            ['12', 'user', true, false],
            ['', 'user', true, false],
            [null, 'user', true, false],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType::fromHash
     *
     * @dataProvider fromHashDataProvider
     */
    public function testFromHash($value, $convertedValue): void
    {
        self::assertSame(
            $convertedValue,
            $this->type->fromHash(
                $this->getParameterDefinition(),
                $value,
            ),
        );
    }

    public static function fromHashDataProvider(): iterable
    {
        return [
            [
                null,
                null,
            ],
            [
                '42',
                42,
            ],
            [
                42,
                42,
            ],
        ];
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType::isValueEmpty
     *
     * @dataProvider emptyDataProvider
     */
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    /**
     * Provider for testing if the value is empty.
     */
    public static function emptyDataProvider(): iterable
    {
        return [
            [null, true],
            [new Location(), false],
        ];
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType::getValueObject
     */
    public function testGetValueObject(): void
    {
        $location = new Location();

        $this->valueObjectProviderMock
            ->expects(self::once())
            ->method('getValueObject')
            ->with(self::identicalTo(42))
            ->willReturn($location);

        /** @var \Netgen\Layouts\Ez\Parameters\ParameterType\LocationType $type */
        $type = $this->type;

        self::assertSame($location, $type->getValueObject(42));
    }
}
