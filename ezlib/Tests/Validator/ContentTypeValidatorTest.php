<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Ez\Validator\ContentTypeValidator;
use Netgen\BlockManager\Ez\Validator\Constraint\Content;

class ContentTypeValidatorTest extends ValidatorTestCase
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

        return new ContentTypeValidator($this->repositoryMock);
    }

    /**
     * @param int $identifier
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($identifier, $isValid)
    {
        if ($identifier !== null) {
            $this->repositoryMock
                ->expects($this->once())
                ->method('sudo')
                ->will(
                    $this->returnCallback(
                        function () use ($identifier) {
                            if (!is_string($identifier) || !in_array($identifier, array('article', 'news'))) {
                                throw new NotFoundException('content type', $identifier);
                            }
                        }
                    )
                );
        }

        $this->assertValid($isValid, $identifier);
    }

    public function validateDataProvider()
    {
        return array(
            array('article', true),
            array('video', false),
            array(5, false),
            array(null, true),
        );
    }
}
