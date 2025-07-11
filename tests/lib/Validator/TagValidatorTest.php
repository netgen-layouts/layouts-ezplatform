<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use Netgen\Layouts\Ez\Validator\Constraint\Tag;
use Netgen\Layouts\Ez\Validator\TagValidator;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag as APITag;
use Netgen\TagsBundle\Core\Repository\TagsService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class TagValidatorTest extends ValidatorTestCase
{
    private MockObject $tagsServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->constraint = new Tag();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\TagValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\TagValidator::validate
     */
    public function testValidateValid(): void
    {
        $this->tagsServiceMock
            ->expects(self::once())
            ->method('loadTag')
            ->with(self::identicalTo(42))
            ->willReturn(new APITag(['id' => 42]));

        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\TagValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\TagValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->tagsServiceMock
            ->expects(self::never())
            ->method('loadTag');

        $this->assertValid(true, null);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\TagValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\TagValidator::validate
     */
    public function testValidateInvalid(): void
    {
        $this->tagsServiceMock
            ->expects(self::once())
            ->method('loadTag')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('tag', 42));

        $this->assertValid(false, 42);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\TagValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Ez\Validator\Constraint\Tag", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\TagValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "scalar", "array" given');

        $this->assertValid(true, []);
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        $this->tagsServiceMock = $this->createPartialMock(TagsService::class, ['loadTag']);

        return new TagValidator($this->tagsServiceMock);
    }
}
