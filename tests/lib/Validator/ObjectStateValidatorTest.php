<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectState as EzObjectState;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Netgen\BlockManager\Ez\Validator\Constraint\ObjectState;
use Netgen\BlockManager\Ez\Validator\ObjectStateValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

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

    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new ObjectState();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        $this->objectStateServiceMock = $this->createMock(ObjectStateService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getObjectStateService']);

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->will(self::returnCallback(function (callable $callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects(self::any())
            ->method('getObjectStateService')
            ->will(self::returnValue($this->objectStateServiceMock));

        return new ObjectStateValidator($this->repositoryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::loadStateIdentifiers
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(string $identifier, array $allowedStates, bool $isValid): void
    {
        $group1 = new ObjectStateGroup(['identifier' => 'group1']);
        $group2 = new ObjectStateGroup(['identifier' => 'group2']);

        $this->objectStateServiceMock
            ->expects(self::at(0))
            ->method('loadObjectStateGroups')
            ->will(self::returnValue([$group1, $group2]));

        $this->objectStateServiceMock
            ->expects(self::at(1))
            ->method('loadObjectStates')
            ->with(self::identicalTo($group1))
            ->will(
                self::returnValue(
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
            ->expects(self::at(2))
            ->method('loadObjectStates')
            ->with(self::identicalTo($group2))
            ->will(
                self::returnValue(
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

        $this->constraint->allowedStates = $allowedStates;
        self::assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::loadStateIdentifiers
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->objectStateServiceMock
            ->expects(self::never())
            ->method('loadObjectStateGroups');

        $this->objectStateServiceMock
            ->expects(self::never())
            ->method('loadObjectStates');

        self::assertValid(true, null);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\ObjectState", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        self::assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        self::assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string with "|" delimiter", "string" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValueFormat(): void
    {
        self::assertValid(true, 'state');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ObjectStateValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "array", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidAllowedStates(): void
    {
        $this->constraint->allowedStates = 42;
        self::assertValid(true, 'group1|state1');
    }

    public function validateDataProvider(): array
    {
        return [
            ['group1|state1', [], true],
            ['group1|state1', ['group2' => true], true],
            ['group1|state1', ['group1' => true], true],
            ['group1|state1', ['group1' => false], false],
            ['group1|state1', ['group1' => []], false],
            ['group1|state1', ['group1' => ['state1']], true],
            ['group1|state1', ['group1' => ['state2']], false],
            ['group1|state1', ['group1' => ['state1', 'state2']], true],
            ['group2|state1', [], true],
            ['group2|state1', ['group2' => true], true],
            ['group2|state1', ['group1' => true], true],
            ['group2|state1', ['group1' => false], true],
            ['group2|state1', ['group1' => []], true],
            ['group2|state1', ['group1' => ['state1']], true],
            ['group2|state1', ['group1' => ['state2']], true],
            ['group2|state1', ['group1' => ['state1', 'state2']], true],
            ['unknown|state1', [], false],
            ['group1|unknown', [], false],
        ];
    }
}
