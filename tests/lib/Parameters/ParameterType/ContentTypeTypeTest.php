<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as EzContentType;
use Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType;
use Netgen\Layouts\Ez\Tests\Validator\RepositoryValidatorFactory;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

use function is_array;

final class ContentTypeTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\eZ\Publish\API\Repository\Repository
     */
    private MockObject $repositoryMock;

    private MockObject $contentTypeServiceMock;

    protected function setUp(): void
    {
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentTypeService']);

        $this->repositoryMock
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->method('getContentTypeService')
            ->willReturn($this->contentTypeServiceMock);

        $this->type = new ContentTypeType();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('ez_content_type', $this->type::getIdentifier());
    }

    /**
     * @param array<string, mixed> $options
     * @param array<string, mixed> $resolvedOptions
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType::configureOptions
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
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType::configureOptions
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
                    'multiple' => false,
                    'types' => [],
                ],
            ],
            [
                [
                    'multiple' => false,
                ],
                [
                    'multiple' => false,
                    'types' => [],
                ],
            ],
            [
                [
                    'multiple' => true,
                ],
                [
                    'multiple' => true,
                    'types' => [],
                ],
            ],
            [
                [
                    'types' => [],
                ],
                [
                    'multiple' => false,
                    'types' => [],
                ],
            ],
            [
                [
                    'types' => [42],
                ],
                [
                    'multiple' => false,
                    'types' => [42],
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
                    'multiple' => 'true',
                ],
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType::getValueConstraints
     *
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, bool $required, bool $isValid): void
    {
        $options = [];

        if ($value !== null) {
            $options = ['multiple' => is_array($value)];

            $this->contentTypeServiceMock
                ->method('loadContentTypeByIdentifier')
                ->willReturnCallback(
                    static function (string $identifier): EzContentType {
                        if ($identifier !== 'other') {
                            return new EzContentType(['identifier' => $identifier]);
                        }

                        throw new NotFoundException('content type', $identifier);
                    },
                );
        }

        $parameter = $this->getParameterDefinition($options, $required);
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
            ['news', false, true],
            [[], false, true],
            [['news'], false, true],
            [['article', 'news'], false, true],
            [['article', 'other'], false, false],
            [['other'], false, false],
            [null, false, true],
            ['news', true, true],
            [[], true, false],
            [['news'], true, true],
            [['article', 'news'], true, true],
            [['article', 'other'], true, false],
            [['other'], true, false],
            [null, true, false],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType::fromHash
     *
     * @dataProvider fromHashDataProvider
     */
    public function testFromHash($value, $convertedValue, bool $multiple): void
    {
        self::assertSame(
            $convertedValue,
            $this->type->fromHash(
                $this->getParameterDefinition(
                    [
                        'multiple' => $multiple,
                    ],
                ),
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
                false,
            ],
            [
                [],
                null,
                false,
            ],
            [
                42,
                42,
                false,
            ],
            [
                [42, 43],
                42,
                false,
            ],
            [
                null,
                null,
                true,
            ],
            [
                [],
                null,
                true,
            ],
            [
                42,
                [42],
                true,
            ],
            [
                [42, 43],
                [42, 43],
                true,
            ],
        ];
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType::isValueEmpty
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
            [[], true],
            [42, false],
            [[42], false],
            [0, false],
            ['42', false],
            ['', false],
        ];
    }
}
