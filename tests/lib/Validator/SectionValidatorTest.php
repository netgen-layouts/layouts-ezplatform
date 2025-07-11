<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section as EzSection;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Netgen\Layouts\Ez\Validator\Constraint\Section;
use Netgen\Layouts\Ez\Validator\SectionValidator;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class SectionValidatorTest extends ValidatorTestCase
{
    private MockObject $repositoryMock;

    private MockObject $sectionServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->constraint = new Section();
    }

    /**
     * @param string[] $allowedSections
     *
     * @covers \Netgen\Layouts\Ez\Validator\SectionValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\SectionValidator::validate
     *
     * @dataProvider  validateDataProvider
     */
    public function testValidate(string $identifier, array $allowedSections, bool $isValid): void
    {
        $this->sectionServiceMock
            ->expects(self::once())
            ->method('loadSectionByIdentifier')
            ->with(self::identicalTo($identifier))
            ->willReturn(
                new EzSection(
                    [
                        'identifier' => $identifier,
                    ],
                ),
            );

        $this->constraint->allowedSections = $allowedSections;
        $this->assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\SectionValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\SectionValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->sectionServiceMock
            ->expects(self::never())
            ->method('loadSectionByIdentifier');

        $this->assertValid(true, null);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\SectionValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\SectionValidator::validate
     */
    public function testValidateInvalid(): void
    {
        $this->sectionServiceMock
            ->expects(self::once())
            ->method('loadSectionByIdentifier')
            ->with(self::identicalTo('unknown'))
            ->willThrowException(new NotFoundException('section', 'unknown'));

        $this->assertValid(false, 'unknown');
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\SectionValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Ez\Validator\Constraint\Section", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\SectionValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "string", "int(eger)?" given$/');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            ['media', [], true],
            ['media', ['media'], true],
            ['media', ['standard'], false],
            ['media', ['media', 'standard'], true],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        $this->sectionServiceMock = $this->createMock(SectionService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getSectionService']);

        $this->repositoryMock
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->method('getSectionService')
            ->willReturn($this->sectionServiceMock);

        return new SectionValidator($this->repositoryMock);
    }
}
