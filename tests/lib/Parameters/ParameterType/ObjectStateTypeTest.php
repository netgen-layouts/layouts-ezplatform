<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Parameters\ParameterType;

use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Core\Repository\Repository;
use Ibexa\Core\Repository\Values\ObjectState\ObjectState as IbexaObjectState;
use Ibexa\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Netgen\Layouts\Ibexa\Parameters\ParameterType\ObjectStateType;
use Netgen\Layouts\Ibexa\Tests\Validator\RepositoryValidatorFactory;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

use function is_array;

#[CoversClass(ObjectStateType::class)]
final class ObjectStateTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    private MockObject&Repository $repositoryMock;

    private MockObject&ObjectStateService $objectStateServiceMock;

    protected function setUp(): void
    {
        $this->objectStateServiceMock = $this->createMock(ObjectStateService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getObjectStateService']);

        $this->repositoryMock
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->method('getObjectStateService')
            ->willReturn($this->objectStateServiceMock);

        $this->type = new ObjectStateType();
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('ibexa_object_state', $this->type::getIdentifier());
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
                [],
                [
                    'multiple' => false,
                    'states' => [],
                ],
            ],
            [
                [
                    'multiple' => false,
                ],
                [
                    'multiple' => false,
                    'states' => [],
                ],
            ],
            [
                [
                    'multiple' => true,
                ],
                [
                    'multiple' => true,
                    'states' => [],
                ],
            ],
            [
                [
                    'states' => [],
                ],
                [
                    'multiple' => false,
                    'states' => [],
                ],
            ],
            [
                [
                    'states' => [42],
                ],
                [
                    'multiple' => false,
                    'states' => [42],
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

    #[DataProvider('validationDataProvider')]
    public function testValidation(mixed $value, bool $required, bool $isValid): void
    {
        $group1 = new ObjectStateGroup(['identifier' => 'group1']);
        $group2 = new ObjectStateGroup(['identifier' => 'group2']);

        $this->objectStateServiceMock
            ->method('loadObjectStateGroups')
            ->willReturn([$group1, $group2]);

        $this->objectStateServiceMock
            ->method('loadObjectStates')
            ->willReturnMap(
                [
                    [
                        $group1,
                        [],
                        [
                            new IbexaObjectState(
                                [
                                    'identifier' => 'state1',
                                ],
                            ),
                            new IbexaObjectState(
                                [
                                    'identifier' => 'state2',
                                ],
                            ),
                        ],
                    ],
                    [
                        $group2,
                        [],
                        [],
                    ],
                ],
            );

        $options = $value !== null ? ['multiple' => is_array($value)] : [];
        $parameter = $this->getParameterDefinition($options, $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    #[DataProvider('validationWithEmptyValuesDataProvider')]
    public function testValidationWithEmptyValues(mixed $value, bool $required, bool $isValid): void
    {
        $this->objectStateServiceMock
            ->expects(self::never())
            ->method('loadObjectStateGroups');

        $this->objectStateServiceMock
            ->expects(self::never())
            ->method('loadObjectStates');

        $options = $value !== null ? ['multiple' => is_array($value)] : [];
        $parameter = $this->getParameterDefinition($options, $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    public static function validationDataProvider(): iterable
    {
        return [
            ['group1|state2', false, true],
            [['group1|state2'], false, true],
            [['group1|state1', 'group1|state2'], false, true],
            [['group1|state1', 'group2|state1'], false, false],
            [['group2|state1'], false, false],
            [['unknown|state1'], false, false],
            [['group1|unknown'], false, false],
            ['group1|state2', true, true],
            [['group1|state2'], true, true],
            [['group1|state1', 'group1|state2'], true, true],
            [['group1|state1', 'group2|state1'], true, false],
            [['group2|state1'], true, false],
            [['unknown|state1'], true, false],
            [['group1|unknown'], true, false],
        ];
    }

    public static function validationWithEmptyValuesDataProvider(): iterable
    {
        return [
            [[], false, true],
            [null, false, true],
            [[], true, false],
            [null, true, false],
        ];
    }

    #[DataProvider('fromHashDataProvider')]
    public function testFromHash(mixed $value, mixed $convertedValue, bool $multiple): void
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

    #[DataProvider('emptyDataProvider')]
    public function testIsValueEmpty(mixed $value, bool $isEmpty): void
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
