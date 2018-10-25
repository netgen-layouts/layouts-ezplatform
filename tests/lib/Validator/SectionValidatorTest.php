<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section as EzSection;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Netgen\BlockManager\Ez\Validator\Constraint\Section;
use Netgen\BlockManager\Ez\Validator\SectionValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class SectionValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $sectionServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new Section();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::validate
     * @dataProvider  validateDataProvider
     */
    public function testValidate(string $identifier, array $allowedSections, bool $isValid): void
    {
        $this->sectionServiceMock
            ->expects(self::once())
            ->method('loadSectionByIdentifier')
            ->with(self::identicalTo($identifier))
            ->will(
                self::returnValue(
                    new EzSection(
                        [
                            'identifier' => $identifier,
                        ]
                    )
                )
            );

        $this->constraint->allowedSections = $allowedSections;
        self::assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->sectionServiceMock
            ->expects(self::never())
            ->method('loadSectionByIdentifier');

        self::assertValid(true, null);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::validate
     */
    public function testValidateInvalid(): void
    {
        $this->sectionServiceMock
            ->expects(self::once())
            ->method('loadSectionByIdentifier')
            ->with(self::identicalTo('unknown'))
            ->will(
                self::throwException(
                    new NotFoundException('section', 'unknown')
                )
            );

        self::assertValid(false, 'unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\BlockManager\\Ez\\Validator\\Constraint\\Section", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        self::assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "integer" given');

        self::assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidAllowedSections(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "array", "integer" given');

        $this->constraint->allowedSections = 42;
        self::assertValid(true, 'media');
    }

    public function validateDataProvider(): array
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
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->will(self::returnCallback(function (callable $callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects(self::any())
            ->method('getSectionService')
            ->will(self::returnValue($this->sectionServiceMock));

        return new SectionValidator($this->repositoryMock);
    }
}
