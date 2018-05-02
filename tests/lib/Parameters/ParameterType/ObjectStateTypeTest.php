<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectState as EzObjectState;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Netgen\BlockManager\Ez\Parameters\ParameterType\ObjectStateType;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class ObjectStateTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \eZ\Publish\API\Repository\Repository&\PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $objectStateServiceMock;

    public function setUp()
    {
        $this->objectStateServiceMock = $this->createMock(ObjectStateService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getObjectStateService']);

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function (callable $callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getObjectStateService')
            ->will($this->returnValue($this->objectStateServiceMock));

        $this->type = new ObjectStateType();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ObjectStateType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('ez_object_state', $this->type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ObjectStateType::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameter = $this->getParameterDefinition($options);
        $this->assertEquals($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ObjectStateType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     *
     * @param array $options
     */
    public function testInvalidOptions($options)
    {
        $this->getParameterDefinition($options);
    }

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return array
     */
    public function validOptionsProvider()
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
     *
     * @return array
     */
    public function invalidOptionsProvider()
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
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ObjectStateType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        if (!empty($value)) {
            $this->objectStateServiceMock
                ->expects($this->at(0))
                ->method('loadObjectStateGroups')
                ->will(
                    $this->returnValue(
                        [
                            new ObjectStateGroup(['identifier' => 'group1']),
                            new ObjectStateGroup(['identifier' => 'group2']),
                        ]
                    )
                );

            $this->objectStateServiceMock
                ->expects($this->at(1))
                ->method('loadObjectStates')
                ->with($this->equalTo(new ObjectStateGroup(['identifier' => 'group1'])))
                ->will(
                    $this->returnValue(
                        [
                            new EzObjectState(
                                [
                                    'identifier' => 'state1',
                                ]
                            ),
                            new EzObjectState(
                                [
                                    'identifier' => 'state2',
                                ]
                            ),
                        ]
                    )
                );

            $this->objectStateServiceMock
                ->expects($this->at(2))
                ->method('loadObjectStates')
                ->with($this->equalTo(new ObjectStateGroup(['identifier' => 'group2'])))
                ->will($this->returnValue([]));
        }

        $options = $value !== null ? ['multiple' => is_array($value)] : [];
        $parameter = $this->getParameterDefinition($options, $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            ['group1|state2', false, true],
            [[], false, true],
            [['group1|state2'], false, true],
            [['group1|state1', 'group1|state2'], false, true],
            [['group1|state1', 'group2|state1'], false, false],
            [['group2|state1'], false, false],
            [null, false, true],
            [['unknown|state1'], false, false],
            [['group1|unknown'], false, false],
            ['group1|state2', true, true],
            [[], true, false],
            [['group1|state2'], true, true],
            [['group1|state1', 'group1|state2'], true, true],
            [['group1|state1', 'group2|state1'], true, false],
            [['group2|state1'], true, false],
            [['unknown|state1'], true, false],
            [['group1|unknown'], true, false],
            [null, true, false],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     * @param bool $multiple
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ObjectStateType::fromHash
     * @dataProvider fromHashProvider
     */
    public function testFromHash($value, $convertedValue, $multiple)
    {
        $this->assertEquals(
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

    public function fromHashProvider()
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
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ObjectStateType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, $isEmpty)
    {
        $this->assertEquals($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    /**
     * Provider for testing if the value is empty.
     *
     * @return array
     */
    public function emptyProvider()
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
