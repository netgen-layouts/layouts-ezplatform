<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator;

use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Core\Repository\Repository;
use Ibexa\Core\Repository\Values\ObjectState\ObjectState as IbexaObjectState;
use Ibexa\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Netgen\Layouts\Ibexa\Validator\Constraint\ObjectState;
use Netgen\Layouts\Ibexa\Validator\ObjectStateValidator;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ObjectStateValidatorTest extends ValidatorTestCase
{
    private MockObject $repositoryMock;

    private MockObject $objectStateServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->constraint = new ObjectState();
    }

    /**
     * @param string[] $allowedStates
     *
     * @covers \Netgen\Layouts\Ibexa\Validator\ObjectStateValidator::__construct
     * @covers \Netgen\Layouts\Ibexa\Validator\ObjectStateValidator::loadStateIdentifiers
     * @covers \Netgen\Layouts\Ibexa\Validator\ObjectStateValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(string $identifier, array $allowedStates, bool $isValid): void
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
                ],
            );

        $this->constraint->allowedStates = $allowedStates;
        $this->assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\ObjectStateValidator::__construct
     * @covers \Netgen\Layouts\Ibexa\Validator\ObjectStateValidator::loadStateIdentifiers
     * @covers \Netgen\Layouts\Ibexa\Validator\ObjectStateValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->objectStateServiceMock
            ->expects(self::never())
            ->method('loadObjectStateGroups');

        $this->objectStateServiceMock
            ->expects(self::never())
            ->method('loadObjectStates');

        $this->assertValid(true, null);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\ObjectStateValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\Layouts\\Ibexa\\Validator\\Constraint\\ObjectState", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\ObjectStateValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "string", "int(eger)?" given$/');

        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\ObjectStateValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValueFormat(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string with "|" delimiter", "string" given');

        $this->assertValid(true, 'state');
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
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->expects(self::any())
            ->method('getObjectStateService')
            ->willReturn($this->objectStateServiceMock);

        return new ObjectStateValidator($this->repositoryMock);
    }
}
