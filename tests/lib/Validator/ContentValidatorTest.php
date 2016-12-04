<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Netgen\BlockManager\Ez\Validator\Constraint\Content;
use Netgen\BlockManager\Ez\Validator\ContentValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContentValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentServiceMock;

    public function setUp()
    {
        parent::setUp();

        $this->constraint = new Content();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidatorInterface
     */
    public function getValidator()
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, array('sudo', 'getContentService'));

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function ($callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getContentService')
            ->will($this->returnValue($this->contentServiceMock));

        return new ContentValidator($this->repositoryMock);
    }

    /**
     * @param int $contentId
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($contentId, $isValid)
    {
        if ($contentId !== null) {
            $this->contentServiceMock
                ->expects($this->once())
                ->method('loadContentInfo')
                ->with($this->equalTo($contentId))
                ->will(
                    $this->returnCallback(
                        function () use ($contentId) {
                            if (!is_int($contentId) || $contentId <= 0 || $contentId > 20) {
                                throw new NotFoundException('content', $contentId);
                            }
                        }
                    )
                );
        }

        $this->assertValid($isValid, $contentId);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
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
