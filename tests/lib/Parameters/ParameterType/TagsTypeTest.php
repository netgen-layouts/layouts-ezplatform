<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Parameters\ParameterType;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Netgen\Layouts\Ibexa\Parameters\ParameterType\TagsType;
use Netgen\Layouts\Ibexa\Tests\Validator\TagsServiceValidatorFactory;
use Netgen\Layouts\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\Repository\TagsService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

#[CoversClass(TagsType::class)]
final class TagsTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    private MockObject&TagsService $tagsServiceMock;

    protected function setUp(): void
    {
        $this->tagsServiceMock = $this->createPartialMock(TagsService::class, ['loadTag', 'loadTagByRemoteId']);

        $this->type = new TagsType($this->tagsServiceMock);
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('netgen_tags', $this->type::getIdentifier());
    }

    /**
     * @param array<string, mixed> $options
     * @param array<string, mixed> $resolvedOptions
     */
    #[DataProvider('validOptionsDataProvider')]
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @param array<string, mixed> $options
     */
    #[DataProvider('invalidOptionsDataProvider')]
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
                [
                ],
                [
                    'min' => null,
                    'max' => null,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'max' => 5,
                ],
                [
                    'min' => null,
                    'max' => 5,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'max' => null,
                ],
                [
                    'min' => null,
                    'max' => null,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'min' => 5,
                ],
                [
                    'min' => 5,
                    'max' => null,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'min' => null,
                ],
                [
                    'min' => null,
                    'max' => null,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'min' => 5,
                    'max' => 10,
                    'allow_invalid' => false,
                ],
                [
                    'min' => 5,
                    'max' => 10,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'min' => 5,
                    'max' => 3,
                ],
                [
                    'min' => 5,
                    'max' => 5,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'allow_invalid' => false,
                ],
                [
                    'min' => null,
                    'max' => null,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'allow_invalid' => true,
                ],
                [
                    'min' => null,
                    'max' => null,
                    'allow_invalid' => true,
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
                    'min' => '0',
                ],
                [
                    'min' => -5,
                ],
                [
                    'min' => 0,
                ],
                [
                    'max' => '0',
                ],
                [
                    'max' => -5,
                ],
                [
                    'max' => 0,
                ],
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
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    #[DataProvider('fromHashDataProvider')]
    public function testFromHash(mixed $value, mixed $convertedValue): void
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
                [42, '24'],
                [42, 24],
            ],
        ];
    }

    public function testExport(): void
    {
        $this->tagsServiceMock
            ->expects(self::once())
            ->method('loadTag')
            ->with(self::identicalTo(42))
            ->willReturn(new Tag(['remoteId' => 'abc']));

        self::assertSame('abc', $this->type->export($this->getParameterDefinition(), 42));
    }

    public function testExportWithNonExistingTag(): void
    {
        $this->tagsServiceMock
            ->expects(self::once())
            ->method('loadTag')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('tag', 42));

        self::assertNull($this->type->export($this->getParameterDefinition(), 42));
    }

    public function testImport(): void
    {
        $this->tagsServiceMock
            ->expects(self::once())
            ->method('loadTagByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn(new Tag(['id' => 42]));

        self::assertSame(42, $this->type->import($this->getParameterDefinition(), 'abc'));
    }

    public function testImportWithNonExistingTag(): void
    {
        $this->tagsServiceMock
            ->expects(self::once())
            ->method('loadTagByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willThrowException(new NotFoundException('tag', 'abc'));

        self::assertNull($this->type->import($this->getParameterDefinition(), 'abc'));
    }

    #[DataProvider('validationDataProvider')]
    public function testValidation(mixed $values, bool $required, bool $isValid): void
    {
        if ($values !== null) {
            $this->tagsServiceMock
                ->method('loadTag')
                ->willReturnCallback(
                    static fn (int $id): Tag => match (true) {
                        $id > 0 => new Tag(['id' => $id]),
                        default => throw new NotFoundException('tag', $id),
                    },
                );
        }

        $parameter = $this->getParameterDefinition(['min' => 1, 'max' => 3], $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new TagsServiceValidatorFactory($this->tagsServiceMock))
            ->getValidator();

        $errors = $validator->validate($values, $this->type->getConstraints($parameter, $values));
        self::assertSame($isValid, $errors->count() === 0);
    }

    public static function validationDataProvider(): iterable
    {
        return [
            [[12], false, true],
            [[12, 13, 14, 15], false, false],
            [[-12], false, false],
            [[0], false, false],
            [[null], false, false],
            [[], false, false],
            [null, false, true],
            [[12], true, true],
            [[12, 13, 14, 15], true, false],
            [[-12], true, false],
            [[0], true, false],
            [[null], true, false],
            [[], true, false],
            [null, true, false],
        ];
    }
}
