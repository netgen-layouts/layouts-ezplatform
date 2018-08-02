<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\ConditionType;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as EzContentType;
use Netgen\BlockManager\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class ContentTypeTest extends TestCase
{
    /**
     * @var \eZ\Publish\API\Repository\Repository&\PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType
     */
    private $conditionType;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentExtractorMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeServiceMock;

    public function setUp(): void
    {
        $this->contentExtractorMock = $this->createMock(ContentExtractorInterface::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentTypeService']);

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function (callable $callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getContentTypeService')
            ->will($this->returnValue($this->contentTypeServiceMock));

        $this->conditionType = new ContentType(
            $this->contentExtractorMock,
            $this->contentTypeServiceMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::__construct
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::getType
     */
    public function testGetType(): void
    {
        $this->assertSame('ez_content_type', $this->conditionType::getType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::getConstraints
     */
    public function testValidation(): void
    {
        $this->contentTypeServiceMock
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($this->identicalTo('identifier'))
            ->will($this->returnValue(new EzContentType()));

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(['identifier'], $this->conditionType->getConstraints());
        $this->assertCount(0, $errors);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::getConstraints
     */
    public function testValidationWithInvalidValue(): void
    {
        $this->contentTypeServiceMock
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($this->identicalTo('unknown'))
            ->will($this->throwException(new NotFoundException('content type', 'unknown')));

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(['unknown'], $this->conditionType->getConstraints());
        $this->assertNotCount(0, $errors);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::matches
     *
     * @param mixed $value
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($value, bool $matches): void
    {
        $request = Request::create('/');

        $content = new Content(
            [
                'versionInfo' => new VersionInfo(
                    [
                        'contentInfo' => new ContentInfo(
                            [
                                'contentTypeId' => 24,
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->contentExtractorMock
            ->expects($this->any())
            ->method('extractContent')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($content));

        $this->contentTypeServiceMock
            ->expects($this->any())
            ->method('loadContentType')
            ->with($this->identicalTo(24))
            ->will(
                $this->returnValue(
                    new EzContentType(
                        [
                            'identifier' => 'article',
                            'fieldDefinitions' => [],
                        ]
                    )
                )
            );

        $this->assertSame($matches, $this->conditionType->matches($request, $value));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::matches
     */
    public function testMatchesWithNoContent(): void
    {
        $request = Request::create('/');

        $this->contentExtractorMock
            ->expects($this->any())
            ->method('extractContent')
            ->with($this->identicalTo($request))
            ->will($this->returnValue(null));

        $this->assertFalse($this->conditionType->matches($request, ['article']));
    }

    public function matchesProvider(): array
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
