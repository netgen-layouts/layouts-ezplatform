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

class EzPublishRuntimeTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentTypeServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translationHelperMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime
     */
    protected $runtime;

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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadLocation
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadContent
     */
    public function testGetLocationPath()
    {
        $this->mockServices();

        $this->assertEquals(
            array(
                'Content name 102',
                'Content name 142',
                'Content name 184',
            ),
            $this->runtime->getLocationPath(22)
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::getLocationPath
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadLocation
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadContent
     */
    public function testGetLocationPathWithException()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(22))
            ->will($this->throwException(new Exception()));

        $this->assertEquals(array(), $this->runtime->getLocationPath(22));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::getContentTypeName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime::loadContentType
     */
    public function testGetContentTypeName()
    {
        $this->mockServices();

        $this->assertEquals('Content type some_type', $this->runtime->getContentTypeName('some_type'));
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

    protected function prepareRepositoryMock()
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        $this->repositoryMock = $this->createPartialMock(
            Repository::class,
            array(
                'sudo',
                'getContentService',
                'getLocationService',
                'getContentTypeService',
            )
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

    protected function mockServices()
    {
        $this->locationServiceMock
            ->expects($this->any())
            ->method('loadLocation')
            ->will(
                $this->returnCallback(
                    function ($locationId) {
                        return new Location(
                            array(
                                'path' => array(1, 2, 42, 84),
                                'contentInfo' => new ContentInfo(
                                    array(
                                        'id' => $locationId + 100,
                                    )
                                ),
                            )
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
                            array(
                                'versionInfo' => new VersionInfo(
                                    array(
                                        'contentInfo' => new ContentInfo(
                                            array(
                                                'id' => $contentId,
                                                'name' => 'Content name ' . $contentId,
                                            )
                                        ),
                                    )
                                ),
                            )
                        );
                    }
                )
            );

        $this->contentTypeServiceMock
            ->expects($this->any())
            ->method('loadContentTypeByIdentifier')
            ->will(
                $this->returnCallback(
                    function ($identifier) {
                        return new ContentType(
                            array(
                                'identifier' => $identifier,
                                'names' => array(
                                    'cro-HR' => 'Content type ' . $identifier,
                                ),
                                'fieldDefinitions' => array(),
                            )
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

        $this->translationHelperMock
            ->expects($this->any())
            ->method('getTranslatedByMethod')
            ->will(
                $this->returnCallback(
                    function ($object, $method) {
                        return $object->$method('cro-HR');
                    }
                )
            );
    }
}
