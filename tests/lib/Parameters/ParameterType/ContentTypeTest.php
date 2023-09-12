<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as EzContentType;
use Netgen\Layouts\Ez\Parameters\ParameterType\ContentType;
use Netgen\Layouts\Ez\Tests\Validator\RepositoryValidatorFactory;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ValueObjectProviderInterface;
use Netgen\Layouts\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

use function is_int;

final class ContentTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\eZ\Publish\API\Repository\Repository
     */
    private MockObject $repositoryMock;

    private MockObject $valueObjectProviderMock;

    private MockObject $contentServiceMock;

    private MockObject $contentTypeServiceMock;

    protected function setUp(): void
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentService', 'getContentTypeService']);
        $this->valueObjectProviderMock = $this->createMock(ValueObjectProviderInterface::class);

        $this->repositoryMock
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->method('getContentService')
            ->willReturn($this->contentServiceMock);

        $this->repositoryMock
            ->method('getContentTypeService')
            ->willReturn($this->contentTypeServiceMock);

        $this->contentTypeServiceMock
            ->method('loadContentType')
            ->willReturnCallback(
                static function (int $type): EzContentType {
                    if ($type === 24) {
                        return new EzContentType(['identifier' => 'user']);
                    }

                    if ($type === 42) {
                        return new EzContentType(['identifier' => 'image']);
                    }

                    return new EzContentType(['identifier' => 'article']);
                },
            );

        $this->type = new ContentType($this->repositoryMock, $this->valueObjectProviderMock);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType::__construct
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('ez_content', $this->type::getIdentifier());
    }

    /**
     * @param array<string, mixed> $options
     * @param array<string, mixed> $resolvedOptions
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType::configureOptions
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
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType::configureOptions
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
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType::export
     */
    public function testExport(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->willReturn(new ContentInfo(['remoteId' => 'abc']));

        self::assertSame('abc', $this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType::export
     */
    public function testExportWithNonExistingContent(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('contentInfo', 42));

        self::assertNull($this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType::import
     */
    public function testImport(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfoByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn(new ContentInfo(['id' => 42]));

        self::assertSame(42, $this->type->import($this->getParameterDefinition(), 'abc'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType::import
     */
    public function testImportWithNonExistingContent(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfoByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willThrowException(new NotFoundException('contentInfo', 'abc'));

        self::assertNull($this->type->import($this->getParameterDefinition(), 'abc'));
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType::getValueConstraints
     *
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, int $type, bool $required, bool $isValid): void
    {
        if ($value !== null) {
            $this->contentServiceMock
                ->expects(self::once())
                ->method('loadContentInfo')
                ->with(self::identicalTo((int) $value))
                ->willReturnCallback(
                    static function () use ($value, $type): ContentInfo {
                        if (!is_int($value) || $value <= 0) {
                            throw new NotFoundException('content', $value);
                        }

                        return new ContentInfo(['id' => $value, 'contentTypeId' => $type]);
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
     * @param mixed $convertedValue
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType::fromHash
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
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType::isValueEmpty
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
            [new ContentInfo(), false],
        ];
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType::getValueObject
     */
    public function testGetValueObject(): void
    {
        $content = new Content();

        $this->valueObjectProviderMock
            ->expects(self::once())
            ->method('getValueObject')
            ->with(self::identicalTo(42))
            ->willReturn($content);

        /** @var \Netgen\Layouts\Ez\Parameters\ParameterType\ContentType $type */
        $type = $this->type;

        self::assertSame($content, $type->getValueObject(42));
    }
}
