<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Ez\Validator\ContentValidator;
use Netgen\BlockManager\Ez\Validator\Constraint\Content;

class ContentValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    public function setUp()
    {
        parent::setUp();

        $this->constraint = new Content();
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        $this->repositoryMock = $this->createMock(Repository::class);

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
            $this->repositoryMock
                ->expects($this->once())
                ->method('sudo')
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
