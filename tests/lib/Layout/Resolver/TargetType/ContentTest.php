<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Layout\Resolver\TargetType;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Repository;
use Ibexa\Core\Repository\Values\Content\Content as IbexaContent;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Netgen\Layouts\Ibexa\ContentProvider\ContentExtractorInterface;
use Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Content;
use Netgen\Layouts\Ibexa\Tests\Validator\RepositoryValidatorFactory;
use Netgen\Layouts\Ibexa\Utils\RemoteIdConverter;
use Netgen\Layouts\Layout\Resolver\ValueObjectProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class ContentTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Ibexa\Contracts\Core\Repository\Repository
     */
    private MockObject $repositoryMock;

    private MockObject $contentServiceMock;

    private MockObject $contentExtractorMock;

    private MockObject $valueObjectProviderMock;

    private Content $targetType;

    protected function setUp(): void
    {
        $this->contentExtractorMock = $this->createMock(ContentExtractorInterface::class);
        $this->valueObjectProviderMock = $this->createMock(ValueObjectProviderInterface::class);
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentService']);

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->expects(self::any())
            ->method('getContentService')
            ->willReturn($this->contentServiceMock);

        $this->targetType = new Content(
            $this->contentExtractorMock,
            $this->valueObjectProviderMock,
            new RemoteIdConverter($this->repositoryMock),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Content::__construct
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Content::getType
     */
    public function testGetType(): void
    {
        self::assertSame('ibexa_content', $this->targetType::getType());
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Content::getConstraints
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
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Content::getConstraints
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
        self::assertCount(0, $errors);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValue(): void
    {
        $content = new IbexaContent(
            [
                'versionInfo' => new VersionInfo(
                    [
                        'contentInfo' => new ContentInfo(
                            [
                                'id' => 42,
                            ],
                        ),
                    ],
                ),
            ],
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
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Content::provideValue
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

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Content::getValueObject
     */
    public function testGetValueObject(): void
    {
        $content = new IbexaContent();

        $this->valueObjectProviderMock
            ->expects(self::once())
            ->method('getValueObject')
            ->with(self::identicalTo(42))
            ->willReturn($content);

        self::assertSame($content, $this->targetType->getValueObject(42));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Content::export
     */
    public function testExport(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->willReturn(new ContentInfo(['remoteId' => 'abc']));

        self::assertSame('abc', $this->targetType->export(42));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Content::export
     */
    public function testExportWithInvalidValue(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('content', 42));

        self::assertNull($this->targetType->export(42));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Content::import
     */
    public function testImport(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfoByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willReturn(new ContentInfo(['id' => 42]));

        self::assertSame(42, $this->targetType->import('abc'));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\Content::import
     */
    public function testImportWithInvalidValue(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfoByRemoteId')
            ->with(self::identicalTo('abc'))
            ->willThrowException(new NotFoundException('content', 'abc'));

        self::assertSame(0, $this->targetType->import('abc'));
    }
}
