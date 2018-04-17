<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Templating\Twig\Runtime;

use Exception;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime;
use PHPUnit\Framework\TestCase;

final class EzPublishRuntimeTest extends TestCase
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
    private $locationServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $translationHelperMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime
     */
    private $runtime;

    public function setUp()
    {
        $this->prepareRepositoryMock();
        $this->translationHelperMock = $this->createMock(TranslationHelper::class);

        $this->runtime = new EzPublishRuntime(
            $this->repositoryMock,
            $this->translationHelperMock
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::getContentName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadContent
     */
    public function testGetContentName()
    {
        $this->mockServices();

        $this->assertEquals('Content name 42', $this->runtime->getContentName(42));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::getContentName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadContent
     */
    public function testGetContentNameWithException()
    {
        $this->contentServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with($this->equalTo(42))
            ->will($this->throwException(new Exception()));

        $this->assertEquals('', $this->runtime->getContentName(42));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::getLocationPath
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadContent
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadLocation
     */
    public function testGetLocationPath()
    {
        $this->mockServices();

        $this->assertEquals(
            [
                'Content name 102',
                'Content name 142',
                'Content name 184',
            ],
            $this->runtime->getLocationPath(22)
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::getLocationPath
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadContent
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadLocation
     */
    public function testGetLocationPathWithException()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(22))
            ->will($this->throwException(new Exception()));

        $this->assertEquals([], $this->runtime->getLocationPath(22));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::getContentTypeName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadContentType
     */
    public function testGetContentTypeName()
    {
        $this->mockServices();

        $this->contentTypeServiceMock
            ->expects($this->any())
            ->method('loadContentTypeByIdentifier')
            ->will(
                $this->returnCallback(
                    function ($identifier) {
                        return new ContentType(
                            [
                                'identifier' => $identifier,
                                'names' => [
                                    'eng-GB' => 'English content type ' . $identifier,
                                    'cro-HR' => 'Content type ' . $identifier,
                                ],
                                'fieldDefinitions' => [],
                            ]
                        );
                    }
                )
            );

        $this->translationHelperMock
            ->expects($this->any())
            ->method('getTranslatedByMethod')
            ->will(
                $this->returnCallback(
                    function ($object, $method) {
                        return $object->{$method}('cro-HR');
                    }
                )
            );

        $this->assertEquals('Content type some_type', $this->runtime->getContentTypeName('some_type'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::getContentTypeName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadContentType
     */
    public function testGetContentTypeNameWithNoTranslatedName()
    {
        $this->mockServices();

        $this->contentTypeServiceMock
            ->expects($this->any())
            ->method('loadContentTypeByIdentifier')
            ->will(
                $this->returnCallback(
                    function ($identifier) {
                        return new ContentType(
                            [
                                'identifier' => $identifier,
                                'names' => [
                                    'eng-GB' => 'English content type ' . $identifier,
                                    'cro-HR' => 'Content type ' . $identifier,
                                ],
                                'fieldDefinitions' => [],
                            ]
                        );
                    }
                )
            );

        $this->translationHelperMock
            ->expects($this->any())
            ->method('getTranslatedByMethod')
            ->will($this->returnValue(null));

        $this->assertEquals('English content type some_type', $this->runtime->getContentTypeName('some_type'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::getContentTypeName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadContentType
     */
    public function testGetContentTypeNameWithNoNames()
    {
        $this->mockServices();

        $this->contentTypeServiceMock
            ->expects($this->any())
            ->method('loadContentTypeByIdentifier')
            ->will(
                $this->returnCallback(
                    function ($identifier) {
                        return new ContentType(
                            [
                                'identifier' => $identifier,
                                'names' => [],
                                'fieldDefinitions' => [],
                            ]
                        );
                    }
                )
            );

        $this->translationHelperMock
            ->expects($this->any())
            ->method('getTranslatedByMethod')
            ->will($this->returnValue(null));

        $this->assertEquals('', $this->runtime->getContentTypeName('some_type'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::getContentTypeName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadContentType
     */
    public function testGetContentTypeNameWithException()
    {
        $this->contentTypeServiceMock
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($this->equalTo('some_type'))
            ->will($this->throwException(new Exception()));

        $this->assertEquals('', $this->runtime->getContentTypeName('some_type'));
    }

    private function prepareRepositoryMock()
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        $this->repositoryMock = $this->createPartialMock(
            Repository::class,
            [
                'sudo',
                'getContentService',
                'getLocationService',
                'getContentTypeService',
            ]
        );

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will(
                $this->returnCallback(
                    function ($callback) {
                        return $callback($this->repositoryMock);
                    }
                )
            );

        $this->repositoryMock
            ->expects($this->any())
            ->method('getLocationService')
            ->will($this->returnValue($this->locationServiceMock));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getContentService')
            ->will($this->returnValue($this->contentServiceMock));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getContentTypeService')
            ->will($this->returnValue($this->contentTypeServiceMock));
    }

    private function mockServices()
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->will(
                $this->returnCallback(
                    function ($locationId) {
                        return new Location(
                            [
                                'path' => [1, 2, 42, 84],
                                'contentInfo' => new ContentInfo(
                                    [
                                        'id' => $locationId + 100,
                                    ]
                                ),
                            ]
                        );
                    }
                )
            );

        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContent')
            ->will(
                $this->returnCallback(
                    function ($contentId) {
                        return new Content(
                            [
                                'versionInfo' => new VersionInfo(
                                    [
                                        'contentInfo' => new ContentInfo(
                                            [
                                                'id' => $contentId,
                                                'name' => 'Content name ' . $contentId,
                                            ]
                                        ),
                                    ]
                                ),
                            ]
                        );
                    }
                )
            );

        $this->translationHelperMock
            ->expects($this->any())
            ->method('getTranslatedContentName')
            ->will(
                $this->returnCallback(
                    function (Content $content) {
                        return $content->contentInfo->name;
                    }
                )
            );
    }
}
