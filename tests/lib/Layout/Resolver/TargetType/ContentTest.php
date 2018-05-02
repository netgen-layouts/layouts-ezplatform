<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetType;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Content as EzContent;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Netgen\BlockManager\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
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
     * @var \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content
     */
    private $targetType;

    public function setUp()
    {
        $this->contentExtractorMock = $this->createMock(ContentExtractorInterface::class);
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

        $this->targetType = new Content($this->contentExtractorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content::__construct
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content::getType
     */
    public function testGetType()
    {
        $this->assertEquals('ezcontent', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        if ($value !== null) {
            $this->contentServiceMock
                ->expects($this->once())
                ->method('loadContentInfo')
                ->with($this->equalTo($value))
                ->will(
                    $this->returnCallback(
                        function () use ($value) {
                            if (!is_int($value) || $value > 20) {
                                throw new NotFoundException('location', $value);
                            }
                        }
                    )
                );
        }

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValue()
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
            ->expects($this->any())
            ->method('extractContent')
            ->with($this->equalTo($request))
            ->will($this->returnValue($content));

        $this->assertEquals(42, $this->targetType->provideValue($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValueWithNoContent()
    {
        $request = Request::create('/');

        $this->contentExtractorMock
            ->expects($this->any())
            ->method('extractContent')
            ->with($this->equalTo($request))
            ->will($this->returnValue(null));

        $this->assertNull($this->targetType->provideValue($request));
    }

    /**
     * Extractor for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            [12, true],
            [24, false],
            [-12, false],
            [0, false],
            ['12', false],
            ['', false],
            [null, false],
        ];
    }
}
