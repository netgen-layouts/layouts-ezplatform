<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use Netgen\BlockManager\Ez\Validator\Constraint\Tag;
use Netgen\BlockManager\Ez\Validator\TagValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\TagsBundle\Core\Repository\TagsService;
use Symfony\Component\Validator\Constraints\NotBlank;

class TagValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $tagsServiceMock;

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
     * @param int $tagId
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
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\TagValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
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
