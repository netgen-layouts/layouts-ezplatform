<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Parameters\ParameterType;

use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\Section as IbexaSection;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Repository;
use Netgen\Layouts\Ibexa\Parameters\ParameterType\SectionType;
use Netgen\Layouts\Ibexa\Tests\Validator\RepositoryValidatorFactory;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

use function in_array;
use function is_array;
use function is_string;

final class SectionTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Ibexa\Contracts\Core\Repository\Repository
     */
    private MockObject $repositoryMock;

    private MockObject $sectionServiceMock;

    protected function setUp(): void
    {
        $this->sectionServiceMock = $this->createMock(SectionService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getSectionService']);

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->expects(self::any())
            ->method('getSectionService')
            ->willReturn($this->sectionServiceMock);

        $this->type = new SectionType();
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Parameters\ParameterType\SectionType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('ibexa_section', $this->type::getIdentifier());
    }

    /**
     * @param array<string, mixed> $options
     * @param array<string, mixed> $resolvedOptions
     *
     * @covers \Netgen\Layouts\Ibexa\Parameters\ParameterType\SectionType::configureOptions
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
     * @covers \Netgen\Layouts\Ibexa\Parameters\ParameterType\SectionType::configureOptions
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
    public function validOptionsDataProvider(): array
    {
        return [
            [
                [],
                [
                    'multiple' => false,
                    'sections' => [],
                ],
            ],
            [
                [
                    'multiple' => false,
                ],
                [
                    'multiple' => false,
                    'sections' => [],
                ],
            ],
            [
                [
                    'multiple' => true,
                ],
                [
                    'multiple' => true,
                    'sections' => [],
                ],
            ],
            [
                [
                    'sections' => [],
                ],
                [
                    'multiple' => false,
                    'sections' => [],
                ],
            ],
            [
                [
                    'sections' => ['media'],
                ],
                [
                    'multiple' => false,
                    'sections' => ['media'],
                ],
            ],
        ];
    }

    /**
     * Provider for testing invalid parameter attributes.
     */
    public function invalidOptionsDataProvider(): array
    {
        return [
            [
                [
                    'multiple' => 'true',
                ],
                [
                    'undefined_value' => 'Value',
                ],
                [
                    'sections' => 42,
                ],
                [
                    'sections' => [42],
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Ibexa\Parameters\ParameterType\SectionType::getValueConstraints
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, bool $required, bool $isValid): void
    {
        $args = [];
        $returns = [];
        $options = [];

        if ($value !== null) {
            $options = ['multiple' => is_array($value)];
            foreach ((array) $value as $index => $identifier) {
                $args[] = [self::identicalTo($identifier)];
                $returns[] = self::returnCallback(
                    static function () use ($identifier): IbexaSection {
                        if (!is_string($identifier) || !in_array($identifier, ['media', 'standard'], true)) {
                            throw new NotFoundException('content type', $identifier);
                        }

                        return new IbexaSection(
                            [
                                'identifier' => $identifier,
                            ],
                        );
                    },
                );
            }
        }

        $this->sectionServiceMock
            ->method('loadSectionByIdentifier')
            ->withConsecutive(...$args)
            ->willReturnOnConsecutiveCalls(...$returns);

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
    public function validationDataProvider(): array
    {
        return [
            ['standard', false, true],
            [[], false, true],
            [['standard'], false, true],
            [['media', 'standard'], false, true],
            [['media', 'other'], false, false],
            [['other'], false, false],
            [null, false, true],
            ['standard', true, true],
            [[], true, false],
            [['standard'], true, true],
            [['media', 'standard'], true, true],
            [['media', 'other'], true, false],
            [['other'], true, false],
            [null, true, false],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     *
     * @covers \Netgen\Layouts\Ibexa\Parameters\ParameterType\SectionType::fromHash
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

    public function fromHashDataProvider(): array
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
     * @covers \Netgen\Layouts\Ibexa\Parameters\ParameterType\SectionType::isValueEmpty
     * @dataProvider emptyDataProvider
     */
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    /**
     * Provider for testing if the value is empty.
     */
    public function emptyDataProvider(): array
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
