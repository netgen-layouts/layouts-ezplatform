<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectState as EzObjectState;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Netgen\BlockManager\Ez\Validator\Constraint\ObjectState;
use Netgen\BlockManager\Ez\Validator\ObjectStateValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ObjectStateValidatorTest extends ValidatorTestCase
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
        parent::setUp();

        $this->constraint = new ObjectState();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidatorInterface
     */
    public function getValidator()
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

        return new ObjectStateValidator($this->repositoryMock);
    }

    /**
     * @param string|null $identifier
     * @param array $allowedStates
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::validate
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::loadStateIdentifiers
     * @dataProvider validateDataProvider
     */
    public function testValidate($identifier, $allowedStates, $isValid)
    {
        if ($identifier !== null) {
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
        }

        $this->constraint->allowedStates = $allowedStates;
        $this->assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\ObjectState", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string with "|" delimiter", "string" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValueFormat()
    {
        $this->assertValid(true, 'state');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "array", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidAllowedStates()
    {
        $this->constraint->allowedStates = 42;
        $this->assertValid(true, 'group1|state1');
    }

    public function validateDataProvider()
    {
        return array(
            array('group1|state1', array(), true),
            array('group1|state1', array('group2' => true), true),
            array('group1|state1', array('group1' => true), true),
            array('group1|state1', array('group1' => false), false),
            array('group1|state1', array('group1' => array()), false),
            array('group1|state1', array('group1' => array('state1')), true),
            array('group1|state1', array('group1' => array('state2')), false),
            array('group1|state1', array('group1' => array('state1', 'state2')), true),
            array('group2|state1', array(), true),
            array('group2|state1', array('group2' => true), true),
            array('group2|state1', array('group1' => true), true),
            array('group2|state1', array('group1' => false), true),
            array('group2|state1', array('group1' => array()), true),
            array('group2|state1', array('group1' => array('state1')), true),
            array('group2|state1', array('group1' => array('state2')), true),
            array('group2|state1', array('group1' => array('state1', 'state2')), true),
            array('unknown|state1', array(), false),
            array('group1|unknown', array(), false),
            array(null, array(), true),
        );
    }
}
