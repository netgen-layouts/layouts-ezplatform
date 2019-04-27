<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectState as EzObjectState;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Netgen\Layouts\Ez\Validator\Constraint\ObjectState;
use Netgen\Layouts\Ez\Validator\ObjectStateValidator;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->constraint = new ObjectState();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ObjectStateValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\ObjectStateValidator::loadStateIdentifiers
     * @covers \Netgen\Layouts\Ez\Validator\ObjectStateValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(string $identifier, array $allowedStates, bool $isValid): void
    {
        $group1 = new ObjectStateGroup(['identifier' => 'group1']);
        $group2 = new ObjectStateGroup(['identifier' => 'group2']);

        $this->objectStateServiceMock
            ->expects(self::at(0))
            ->method('loadObjectStateGroups')
            ->willReturn([$group1, $group2]);

        $this->objectStateServiceMock
            ->expects(self::at(1))
            ->method('loadObjectStates')
            ->with(self::identicalTo($group1))
            ->willReturn(
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
            );

        $this->objectStateServiceMock
            ->expects(self::at(2))
            ->method('loadObjectStates')
            ->with(self::identicalTo($group2))
            ->willReturn(
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
            );

        $this->constraint->allowedStates = $allowedStates;
        self::assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ObjectStateValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\ObjectStateValidator::loadStateIdentifiers
     * @covers \Netgen\Layouts\Ez\Validator\ObjectStateValidator::validate
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
     * @covers \Netgen\Layouts\Ez\Validator\ObjectStateValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\Layouts\\Ez\\Validator\\Constraint\\ObjectState", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        self::assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ObjectStateValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "integer" given');

        self::assertValid(true, 42);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ObjectStateValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValueFormat(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string with "|" delimiter", "string" given');

        self::assertValid(true, 'state');
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ObjectStateValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidAllowedStates(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "array", "integer" given');

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

    protected function getValidator(): ConstraintValidatorInterface
    {
        $this->objectStateServiceMock = $this->createMock(ObjectStateService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getObjectStateService']);

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
            ->method('getObjectStateService')
            ->willReturn($this->objectStateServiceMock);

        return new ObjectStateValidator($this->repositoryMock);
    }
}
