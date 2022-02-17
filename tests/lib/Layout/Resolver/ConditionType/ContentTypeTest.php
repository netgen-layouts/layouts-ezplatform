<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Layout\Resolver\ConditionType;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Repository;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\ContentType\ContentType as IbexaContentType;
use Netgen\Layouts\Ibexa\ContentProvider\ContentExtractorInterface;
use Netgen\Layouts\Ibexa\Layout\Resolver\ConditionType\ContentType;
use Netgen\Layouts\Ibexa\Tests\Validator\RepositoryValidatorFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class ContentTypeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Ibexa\Contracts\Core\Repository\Repository
     */
    private MockObject $repositoryMock;

    private ContentType $conditionType;

    private MockObject $contentExtractorMock;

    private MockObject $contentTypeServiceMock;

    protected function setUp(): void
    {
        $this->contentExtractorMock = $this->createMock(ContentExtractorInterface::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentTypeService']);

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->expects(self::any())
            ->method('getContentTypeService')
            ->willReturn($this->contentTypeServiceMock);

        $this->conditionType = new ContentType(
            $this->contentExtractorMock,
        );
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ConditionType\ContentType::__construct
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ConditionType\ContentType::getType
     */
    public function testGetType(): void
    {
        self::assertSame('ibexa_content_type', $this->conditionType::getType());
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ConditionType\ContentType::getConstraints
     */
    public function testValidation(): void
    {
        $this->contentTypeServiceMock
            ->expects(self::once())
            ->method('loadContentTypeByIdentifier')
            ->with(self::identicalTo('identifier'))
            ->willReturn(new IbexaContentType());

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(['identifier'], $this->conditionType->getConstraints());
        self::assertCount(0, $errors);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ConditionType\ContentType::getConstraints
     */
    public function testValidationWithInvalidValue(): void
    {
        $this->contentTypeServiceMock
            ->expects(self::once())
            ->method('loadContentTypeByIdentifier')
            ->with(self::identicalTo('unknown'))
            ->willThrowException(new NotFoundException('content type', 'unknown'));

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(['unknown'], $this->conditionType->getConstraints());
        self::assertNotCount(0, $errors);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ConditionType\ContentType::matches
     *
     * @param mixed $value
     *
     * @dataProvider matchesDataProvider
     */
    public function testMatches($value, bool $matches): void
    {
        $request = Request::create('/');

        $content = new Content(
            [
                'contentType' => new IbexaContentType(
                    [
                        'identifier' => 'article',
                    ],
                ),
            ],
        );

        $this->contentExtractorMock
            ->expects(self::any())
            ->method('extractContent')
            ->with(self::identicalTo($request))
            ->willReturn($content);

        self::assertSame($matches, $this->conditionType->matches($request, $value));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\ConditionType\ContentType::matches
     */
    public function testMatchesWithNoContent(): void
    {
        $request = Request::create('/');

        $this->contentExtractorMock
            ->expects(self::any())
            ->method('extractContent')
            ->with(self::identicalTo($request))
            ->willReturn(null);

        self::assertFalse($this->conditionType->matches($request, ['article']));
    }

    public function matchesDataProvider(): array
    {
        return [
            ['not_array', false],
            [[], false],
            [['article'], true],
            [['news'], false],
            [['article', 'news'], true],
            [['news', 'article'], true],
            [['news', 'video'], false],
        ];
    }
}
