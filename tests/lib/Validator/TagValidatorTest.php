<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use Netgen\BlockManager\Ez\Validator\Constraint\Tag;
use Netgen\BlockManager\Ez\Validator\TagValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\TagsBundle\Core\Repository\TagsService;
use Symfony\Component\Validator\Constraints\NotBlank;

final class TagValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $tagsServiceMock;

    public function setUp()
    {
        parent::setUp();

        $this->constraint = new Tag();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidatorInterface
     */
    public function getValidator()
    {
        $this->tagsServiceMock = $this->createPartialMock(TagsService::class, array('loadTag'));

        return new TagValidator($this->tagsServiceMock);
    }

    /**
     * @param int|null $tagId
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($tagId, $isValid)
    {
        if ($tagId !== null) {
            $this->tagsServiceMock
                ->expects($this->once())
                ->method('loadTag')
                ->with($this->equalTo($tagId))
                ->will(
                    $this->returnCallback(
                        function () use ($tagId) {
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
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "scalar", "array" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, array());
    }

    public function validateDataProvider()
    {
        return array(
            array(12, true),
            array(25, false),
            array(-12, false),
            array(0, false),
            array('12', false),
            array(null, true),
        );
    }
}
