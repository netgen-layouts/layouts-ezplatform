<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as EzContentType;
use Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class LocationTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \eZ\Publish\API\Repository\Repository&\PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $locationServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeServiceMock;

    public function setUp(): void
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getLocationService', 'getContentTypeService']);

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->will(self::returnCallback(function (callable $callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects(self::any())
            ->method('getLocationService')
            ->will(self::returnValue($this->locationServiceMock));

        $this->repositoryMock
            ->expects(self::any())
            ->method('getContentTypeService')
            ->will(self::returnValue($this->contentTypeServiceMock));

        $this->contentTypeServiceMock
            ->expects(self::any())
            ->method('loadContentType')
            ->will(
                self::returnCallback(
                    function (int $type): EzContentType {
                        if ($type === 24) {
                            return new EzContentType(['identifier' => 'user']);
                        }

                        if ($type === 42) {
                            return new EzContentType(['identifier' => 'image']);
                        }

                        return new EzContentType(['identifier' => 'article']);
                    }
                )
            );

        $this->type = new LocationType($this->repositoryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::__construct
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('ezlocation', $this->type::getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::configureOptions
     * @dataProvider validOptionsProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     */
    public function testInvalidOptions(array $options): void
    {
        $this->getParameterDefinition($options);
    }

    /**
     * Provider for testing valid parameter attributes.
     */
    public function validOptionsProvider(): array
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
    public function invalidOptionsProvider(): array
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
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::export
     */
    public function testExport(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->will(self::returnValue(new Location(['remoteId' => 'abc'])));

        self::assertSame('abc', $this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::export
     */
    public function testExportWithNonExistingLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->will(self::throwException(new NotFoundException('location', 42)));

        self::assertNull($this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::import
     */
    public function testImport(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->will(self::returnValue(new Location(['id' => 42])));

        self::assertSame(42, $this->type->import($this->getParameterDefinition(), 'abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::import
     */
    public function testImportWithNonExistingLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocationByRemoteId')
            ->with(self::identicalTo('abc'))
            ->will(self::throwException(new NotFoundException('location', 'abc')));

        self::assertNull($this->type->import($this->getParameterDefinition(), 'abc'));
    }

    /**
     * @param mixed $value
     * @param int $type
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, int $type, bool $required, bool $isValid): void
    {
        if ($value !== null) {
            $this->locationServiceMock
                ->expects(self::once())
                ->method('loadLocation')
                ->with(self::identicalTo($value))
                ->will(
                    self::returnCallback(
                        function () use ($value, $type): Location {
                            if (!is_int($value) || $value <= 0) {
                                throw new NotFoundException('location', $value);
                            }

                            return new Location(
                                [
                                    'id' => $value,
                                    'contentInfo' => new ContentInfo(['contentTypeId' => $type]),
                                ]
                            );
                        }
                    )
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
    public function validationProvider(): array
    {
        return [
            [12, 24, false, true],
            [12, 42, false, true],
            [12, 52, false, false],
            [-12, 24, false, false],
            [0, 24, false, false],
            ['12', 24, false, false],
            ['', 24, false, false],
            [null, 24, false, true],
            [12, 24, true, true],
            [12, 42, true, true],
            [12, 52, true, false],
            [-12, 24, true, false],
            [0, 24, true, false],
            ['12', 24, true, false],
            ['', 24, true, false],
            [null, 24, true, false],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    /**
     * Provider for testing if the value is empty.
     */
    public function emptyProvider(): array
    {
        return [
            [null, true],
            [new Location(), false],
        ];
    }
}
