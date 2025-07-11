<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\Layouts\Ez\Validator\Constraint\Content;
use Netgen\Layouts\Ez\Validator\ContentValidator;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ContentValidatorTest extends ValidatorTestCase
{
    private MockObject $repositoryMock;

    private MockObject $contentServiceMock;

    private MockObject $contentTypeServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->constraint = new Content(['allowedTypes' => ['user']]);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ContentValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\ContentValidator::validate
     */
    public function testValidateValid(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->willReturn(new ContentInfo(['id' => 42, 'contentTypeId' => 24]));

        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ContentValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\ContentValidator::validate
     */
    public function testValidateInvalidWithWrongType(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->willReturn(new ContentInfo(['id' => 42, 'contentTypeId' => 52]));

        $this->assertValid(false, 42);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ContentValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\ContentValidator::validate
     */
    public function testValidateInvalidWithNonExistingContent(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('content', 42));

        $this->assertValid(false, 42);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ContentValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\ContentValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->contentServiceMock
            ->expects(self::never())
            ->method('loadContentInfo');

        $this->assertValid(true, null);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ContentValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Ez\Validator\Constraint\Content", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ContentValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "scalar", "array" given');

        $this->assertValid(true, []);
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentService', 'getContentTypeService']);

        $this->repositoryMock
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->method('getContentService')
            ->willReturn($this->contentServiceMock);

        $this->repositoryMock
            ->method('getContentTypeService')
            ->willReturn($this->contentTypeServiceMock);

        $this->contentTypeServiceMock
            ->method('loadContentType')
            ->willReturnCallback(
                static fn (int $type): ContentType => $type === 24 ?
                    new ContentType(['identifier' => 'user']) :
                    new ContentType(['identifier' => 'article']),
            );

        return new ContentValidator($this->repositoryMock);
    }
}
