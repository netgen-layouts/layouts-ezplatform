<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectState as EzObjectState;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Netgen\BlockManager\Ez\Parameters\ParameterType\ObjectStateType;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class ObjectStateTypeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $objectStateServiceMock;

    public function setUp()
    {
        $this->objectStateServiceMock = $this->createMock(ObjectStateService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, array('sudo', 'getObjectStateService'));

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function ($callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getObjectStateService')
            ->will($this->returnValue($this->objectStateServiceMock));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ObjectStateType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $type = new ObjectStateType();
        $this->assertEquals('ez_object_state', $type->getIdentifier());
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface
     */
    public function getParameter(array $options = array(), $required = false, $defaultValue = null)
    {
        return new ParameterDefinition(
            array(
                'name' => 'name',
                'type' => new ObjectStateType(),
                'options' => $options,
                'isRequired' => $required,
                'defaultValue' => $defaultValue,
            )
        );
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
        $parameter = $this->getParameter($options);
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
        $this->getParameter($options);
    }

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return array
     */
    public function validOptionsProvider()
    {
        return array(
            array(
                array(),
                array(
                    'multiple' => false,
                    'states' => array(),
                ),
            ),
            array(
                array(
                    'multiple' => false,
                ),
                array(
                    'multiple' => false,
                    'states' => array(),
                ),
            ),
            array(
                array(
                    'multiple' => true,
                ),
                array(
                    'multiple' => true,
                    'states' => array(),
                ),
            ),
            array(
                array(
                    'states' => array(),
                ),
                array(
                    'multiple' => false,
                    'states' => array(),
                ),
            ),
            array(
                array(
                    'states' => array(42),
                ),
                array(
                    'multiple' => false,
                    'states' => array(42),
                ),
            ),
        );
    }

    /**
     * Provider for testing invalid parameter attributes.
     *
     * @return array
     */
    public function invalidOptionsProvider()
    {
        return array(
            array(
                array(
                    'multiple' => 'true',
                ),
                array(
                    'undefined_value' => 'Value',
                ),
            ),
        );
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
                        array(
                            new ObjectStateGroup(array('identifier' => 'group1')),
                            new ObjectStateGroup(array('identifier' => 'group2')),
                        )
                    )
                );

            $this->objectStateServiceMock
                ->expects($this->at(1))
                ->method('loadObjectStates')
                ->with($this->equalTo(new ObjectStateGroup(array('identifier' => 'group1'))))
                ->will(
                    $this->returnValue(
                        array(
                            new EzObjectState(
                                array(
                                    'identifier' => 'state1',
                                )
                            ),
                            new EzObjectState(
                                array(
                                    'identifier' => 'state2',
                                )
                            ),
                        )
                    )
                );

            $this->objectStateServiceMock
                ->expects($this->at(2))
                ->method('loadObjectStates')
                ->with($this->equalTo(new ObjectStateGroup(array('identifier' => 'group2'))))
                ->will($this->returnValue(array()));
        }

        $type = new ObjectStateType();

        $options = $value !== null ? array('multiple' => is_array($value)) : array();
        $parameter = $this->getParameter($options, $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array('group1|state2', false, true),
            array(array(), false, true),
            array(array('group1|state2'), false, true),
            array(array('group1|state1', 'group1|state2'), false, true),
            array(array('group1|state1', 'group2|state1'), false, false),
            array(array('group2|state1'), false, false),
            array(null, false, true),
            array(array('unknown|state1'), false, false),
            array(array('group1|unknown'), false, false),
            array('group1|state2', true, true),
            array(array(), true, false),
            array(array('group1|state2'), true, true),
            array(array('group1|state1', 'group1|state2'), true, true),
            array(array('group1|state1', 'group2|state1'), true, false),
            array(array('group2|state1'), true, false),
            array(array('unknown|state1'), true, false),
            array(array('group1|unknown'), true, false),
            array(null, true, false),
        );
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
        $type = new ObjectStateType();

        $this->assertEquals(
            $convertedValue,
            $type->fromHash(
                new ParameterDefinition(
                    array(
                        'type' => $type,
                        'options' => array(
                            'multiple' => $multiple,
                        ),
                    )
                ),
                $value
            )
        );
    }

    public function fromHashProvider()
    {
        return array(
            array(
                null,
                null,
                false,
            ),
            array(
                array(),
                null,
                false,
            ),
            array(
                42,
                42,
                false,
            ),
            array(
                array(42, 43),
                42,
                false,
            ),
            array(
                null,
                null,
                true,
            ),
            array(
                array(),
                null,
                true,
            ),
            array(
                42,
                array(42),
                true,
            ),
            array(
                array(42, 43),
                array(42, 43),
                true,
            ),
        );
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
        $type = new ObjectStateType();
        $this->assertEquals($isEmpty, $type->isValueEmpty(new ParameterDefinition(), $value));
    }

    /**
     * Provider for testing if the value is empty.
     *
     * @return array
     */
    public function emptyProvider()
    {
        return array(
            array(null, true),
            array(array(), true),
            array(42, false),
            array(array(42), false),
            array(0, false),
            array('42', false),
            array('', false),
        );
    }
}
