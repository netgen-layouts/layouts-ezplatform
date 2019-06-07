<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\TargetType;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Content as EzContent;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Netgen\Layouts\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\Layouts\Ez\Layout\Resolver\TargetType\Content;
use Netgen\Layouts\Ez\Tests\Validator\RepositoryValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class ContentTest extends TestCase
{
    /**
     * @var \eZ\Publish\API\Repository\Repository&\PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentExtractorMock;

    /**
     * @var \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Content
     */
    private $targetType;

    protected function setUp(): void
    {
        $this->contentExtractorMock = $this->createMock(ContentExtractorInterface::class);
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentService']);

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                function (callable $callback) {
                    return $callback($this->repositoryMock);
                }
            );

        $this->repositoryMock
            ->expects(self::any())
            ->method('getContentService')
            ->willReturn($this->contentServiceMock);

        $this->targetType = new Content($this->contentExtractorMock);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Content::__construct
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Content::getType
     */
    public function testGetType(): void
    {
        self::assertSame('ez_content', $this->targetType::getType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Content::getConstraints
     */
    public function testValidation(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->willReturn(new ContentInfo());

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(42, $this->targetType->getConstraints());
        self::assertCount(0, $errors);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Content::getConstraints
     */
    public function testValidationWithInvalidValue(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('content', 42));

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(42, $this->targetType->getConstraints());
        self::assertNotCount(0, $errors);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValue(): void
    {
        $content = new EzContent(
            [
                'versionInfo' => new VersionInfo(
                    [
                        'contentInfo' => new ContentInfo(
                            [
                                'id' => 42,
                            ]
                        ),
                    ]
                ),
            ]
        );

        $request = Request::create('/');

        $this->contentExtractorMock
            ->expects(self::any())
            ->method('extractContent')
            ->with(self::identicalTo($request))
            ->willReturn($content);

        self::assertSame(42, $this->targetType->provideValue($request));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValueWithNoContent(): void
    {
        $request = Request::create('/');

        $this->contentExtractorMock
            ->expects(self::any())
            ->method('extractContent')
            ->with(self::identicalTo($request))
            ->willReturn(null);

        self::assertNull($this->targetType->provideValue($request));
    }
}
