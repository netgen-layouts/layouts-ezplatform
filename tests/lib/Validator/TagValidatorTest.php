<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use Netgen\BlockManager\Ez\Validator\Constraint\Tag;
use Netgen\BlockManager\Ez\Validator\TagValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
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
     * @param int|string|null $tagId
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($tagId, bool $isValid): void
    {
        if ($tagId !== null) {
            $this->tagsServiceMock
                ->expects($this->once())
                ->method('loadTag')
                ->with($this->equalTo($tagId))
                ->will(
                    $this->returnCallback(
                        function () use ($tagId): void {
                            if (!is_int($tagId) || $tagId <= 0 || $tagId > 20) {
                                throw new NotFoundException('tag', $tagId);
                            }
                        }
                    )
                );
        }

        $this->assertValid($isValid, $tagId);
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

    public function validateDataProvider(): array
    {
        return [
            [12, true],
            [25, false],
            [-12, false],
            [0, false],
            ['12', false],
            [null, true],
        ];
    }
}
