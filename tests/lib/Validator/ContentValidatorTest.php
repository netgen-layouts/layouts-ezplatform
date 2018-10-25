<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\BlockManager\Ez\Validator\Constraint\Content;
use Netgen\BlockManager\Ez\Validator\ContentValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

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

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new Content(['allowedTypes' => ['user']]);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     */
    public function testValidateValid(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->will(self::returnValue(new ContentInfo(['id' => 42, 'contentTypeId' => 24])));

        self::assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     */
    public function testValidateInvalidWithWrongType(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->will(self::returnValue(new ContentInfo(['id' => 42, 'contentTypeId' => 52])));

        self::assertValid(false, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     */
    public function testValidateInvalidWithNonExistingContent(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->will(self::throwException(new NotFoundException('content', 42)));

        self::assertValid(false, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->contentServiceMock
            ->expects(self::never())
            ->method('loadContentInfo');

        self::assertValid(true, null);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\BlockManager\\Ez\\Validator\\Constraint\\Content", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        self::assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "scalar", "array" given');

        self::assertValid(true, []);
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentService', 'getContentTypeService']);

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->will(self::returnCallback(function (callable $callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects(self::any())
            ->method('getContentService')
            ->will(self::returnValue($this->contentServiceMock));

        $this->repositoryMock
            ->expects(self::any())
            ->method('getContentTypeService')
            ->will(self::returnValue($this->contentTypeServiceMock));

        $this->contentTypeServiceMock
            ->expects(self::any())
            ->method('loadContentType')
            ->will(
                self::returnCallback(
                    function (int $type): ContentType {
                        if ($type === 24) {
                            return new ContentType(['identifier' => 'user']);
                        }

                        return new ContentType(['identifier' => 'article']);
                    }
                )
            );

        return new ContentValidator($this->repositoryMock);
    }
}
