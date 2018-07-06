<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use Netgen\BlockManager\Ez\Validator\Constraint\Tag;
use Netgen\BlockManager\Ez\Validator\TagValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag as APITag;
use Netgen\TagsBundle\Core\Repository\TagsService;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class TagValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $tagsServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new Tag();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        $this->tagsServiceMock = $this->createPartialMock(TagsService::class, ['loadTag']);

        return new TagValidator($this->tagsServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::validate
     */
    public function testValidateValid(): void
    {
        $this->tagsServiceMock
            ->expects($this->once())
            ->method('loadTag')
            ->with($this->identicalTo(42))
            ->will($this->returnValue(new APITag(['id' => 42])));

        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->tagsServiceMock
            ->expects($this->never())
            ->method('loadTag');

        $this->assertValid(true, null);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::validate
     */
    public function testValidateInvalid(): void
    {
        $this->tagsServiceMock
            ->expects($this->once())
            ->method('loadTag')
            ->with($this->identicalTo(42))
            ->will($this->throwException(new NotFoundException('tag', 42)));

        $this->assertValid(false, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\Tag", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "scalar", "array" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->assertValid(true, []);
    }
}
