<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Netgen\BlockManager\Ez\Validator\Constraint\Content;
use Netgen\BlockManager\Ez\Validator\ContentValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class ContentValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new Content();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentService']);

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function (callable $callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getContentService')
            ->will($this->returnValue($this->contentServiceMock));

        return new ContentValidator($this->repositoryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     */
    public function testValidateValid(): void
    {
        $this->contentServiceMock
            ->expects($this->once())
            ->method('loadContentInfo')
            ->with($this->identicalTo(42))
            ->will($this->returnValue(new ContentInfo(['id' => 42])));

        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->contentServiceMock
            ->expects($this->never())
            ->method('loadContentInfo');

        $this->assertValid(true, null);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     */
    public function testValidateInvalid(): void
    {
        $this->contentServiceMock
            ->expects($this->once())
            ->method('loadContentInfo')
            ->with($this->identicalTo(42))
            ->will($this->throwException(new NotFoundException('content', 42)));

        $this->assertValid(false, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\Content", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "scalar", "array" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->assertValid(true, []);
    }
}
