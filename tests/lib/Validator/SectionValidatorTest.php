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

    public function getValidator(): ConstraintValidatorInterface
    {
        $this->sectionServiceMock = $this->createMock(SectionService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getSectionService']);

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function (callable $callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getSectionService')
            ->will($this->returnValue($this->sectionServiceMock));

        return new SectionValidator($this->repositoryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::validate
     * @dataProvider  validateDataProvider
     */
    public function testValidate(string $identifier, array $allowedSections, bool $isValid): void
    {
        $this->sectionServiceMock
            ->expects($this->once())
            ->method('loadSectionByIdentifier')
            ->with($this->identicalTo($identifier))
            ->will(
                $this->returnValue(
                    new EzSection(
                        [
                            'identifier' => $identifier,
                        ]
                    )
                )
            );

        $this->constraint->allowedSections = $allowedSections;
        $this->assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->sectionServiceMock
            ->expects($this->never())
            ->method('loadSectionByIdentifier');

        $this->assertValid(true, null);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::validate
     */
    public function testValidateInvalid(): void
    {
        $this->sectionServiceMock
            ->expects($this->once())
            ->method('loadSectionByIdentifier')
            ->with($this->identicalTo('unknown'))
            ->will(
                $this->throwException(
                    new NotFoundException('section', 'unknown')
                )
            );

        $this->assertValid(false, 'unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\Section", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SectionValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "array", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidAllowedSections(): void
    {
        $this->constraint->allowedSections = 42;
        $this->assertValid(true, 'media');
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
}
