<?php

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
     * @var \PHPUnit\Framework\MockObject\MockObject
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

    public function setUp()
    {
        $this->contentExtractorMock = $this->createMock(ContentExtractorInterface::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentTypeService']);

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function ($callback) {
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
    public function testGetType()
    {
        $this->assertEquals('ez_content_type', $this->conditionType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        if ($value !== null) {
            foreach ($value as $index => $valueItem) {
                $this->contentTypeServiceMock
                    ->expects($this->at($index))
                    ->method('loadContentTypeByIdentifier')
                    ->with($this->equalTo($valueItem))
                    ->will(
                        $this->returnCallback(
                            function () use ($valueItem) {
                                if (!is_string($valueItem) || !in_array($valueItem, ['article', 'news'], true)) {
                                    throw new NotFoundException('content type', $valueItem);
                                }

                                return new EzContentType(
                                    [
                                        'identifier' => $valueItem,
                                    ]
                                );
                            }
                        )
                    );
            }
        }

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::matches
     *
     * @param mixed $value
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($value, $matches)
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
            ->with($this->equalTo($request))
            ->will($this->returnValue($content));

        $this->contentTypeServiceMock
            ->expects($this->any())
            ->method('loadContentType')
            ->with($this->equalTo(24))
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

        $this->assertEquals($matches, $this->conditionType->matches($request, $value));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::matches
     */
    public function testMatchesWithNoContent()
    {
        $request = Request::create('/');

        $this->contentExtractorMock
            ->expects($this->any())
            ->method('extractContent')
            ->with($this->equalTo($request))
            ->will($this->returnValue(false));

        $this->assertFalse($this->conditionType->matches($request, ['article']));
    }

    /**
     * Provider for testing condition type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            [['article'], true],
            [['article', 'news'], true],
            [['article', 'unknown'], false],
            [['unknown'], false],
            [[], false],
            [null, false],
        ];
    }

    /**
     * Provider for {@link self::testMatches}.
     *
     * @return array
     */
    public function matchesProvider()
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
