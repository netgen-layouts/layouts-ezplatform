<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\ConditionType;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as EzContentType;
use Netgen\Layouts\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\Layouts\Ez\Layout\Resolver\ConditionType\ContentType;
use Netgen\Layouts\Ez\Tests\Validator\RepositoryValidatorFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class ContentTypeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\eZ\Publish\API\Repository\Repository
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
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->method('getContentTypeService')
            ->willReturn($this->contentTypeServiceMock);

        $this->conditionType = new ContentType(
            $this->contentExtractorMock,
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\ContentType::__construct
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\ContentType::getType
     */
    public function testGetType(): void
    {
        self::assertSame('ez_content_type', $this->conditionType::getType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\ContentType::getConstraints
     */
    public function testValidation(): void
    {
        $this->contentTypeServiceMock
            ->expects(self::once())
            ->method('loadContentTypeByIdentifier')
            ->with(self::identicalTo('identifier'))
            ->willReturn(new EzContentType());

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(['identifier'], $this->conditionType->getConstraints());
        self::assertCount(0, $errors);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\ContentType::getConstraints
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
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\ContentType::matches
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
                'contentType' => new EzContentType(
                    [
                        'identifier' => 'article',
                    ],
                ),
            ],
        );

        $this->contentExtractorMock
            ->method('extractContent')
            ->with(self::identicalTo($request))
            ->willReturn($content);

        self::assertSame($matches, $this->conditionType->matches($request, $value));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\ContentType::matches
     */
    public function testMatchesWithNoContent(): void
    {
        $request = Request::create('/');

        $this->contentExtractorMock
            ->method('extractContent')
            ->with(self::identicalTo($request))
            ->willReturn(null);

        self::assertFalse($this->conditionType->matches($request, ['article']));
    }

    public static function matchesDataProvider(): iterable
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
