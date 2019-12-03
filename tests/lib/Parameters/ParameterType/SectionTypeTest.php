<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section as EzSection;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Netgen\Layouts\Ez\Parameters\ParameterType\SectionType;
use Netgen\Layouts\Ez\Tests\Validator\RepositoryValidatorFactory;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

final class SectionTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \eZ\Publish\API\Repository\Repository&\PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $sectionServiceMock;

    protected function setUp(): void
    {
        $this->sectionServiceMock = $this->createMock(SectionService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getSectionService']);

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                function (callable $callback) {
                    return $callback($this->repositoryMock);
                }
            );

        $this->repositoryMock
            ->expects(self::any())
            ->method('getSectionService')
            ->willReturn($this->sectionServiceMock);

        $this->type = new SectionType();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\SectionType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('ez_section', $this->type::getIdentifier());
    }

    /**
     * @param array<string, mixed> $options
     * @param array<string, mixed> $resolvedOptions
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\SectionType::configureOptions
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
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\SectionType::configureOptions
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
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\SectionType::getValueConstraints
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, bool $required, bool $isValid): void
    {
        $options = [];
        if ($value !== null) {
            $options = ['multiple' => is_array($value)];
            foreach ((array) $value as $index => $identifier) {
                $this->sectionServiceMock
                    ->expects(self::at($index))
                    ->method('loadSectionByIdentifier')
                    ->with(self::identicalTo($identifier))
                    ->willReturnCallback(
                        static function () use ($identifier): EzSection {
                            if (!is_string($identifier) || !in_array($identifier, ['media', 'standard'], true)) {
                                throw new NotFoundException('content type', $identifier);
                            }

                            return new EzSection(
                                [
                                    'identifier' => $identifier,
                                ]
                            );
                        }
                    );
            }
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
     * @param bool $multiple
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\SectionType::fromHash
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
                    ]
                ),
                $value
            )
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
     * @param bool $isEmpty
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\SectionType::isValueEmpty
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
