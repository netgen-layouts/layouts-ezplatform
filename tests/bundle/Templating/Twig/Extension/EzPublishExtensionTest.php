<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Templating\Twig;

use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Helper\TranslationHelper;
use Twig_SimpleFunction;
use PHPUnit\Framework\TestCase;
use Exception;

class EzPublishExtensionTest extends TestCase
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
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->prepareRepositoryMock();
        $this->translationHelperMock = $this->createMock(TranslationHelper::class);

        $this->extension = new EzPublishExtension(
            $this->repositoryMock,
            $this->translationHelperMock
        );
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

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::getName
     */
    public function testGetName()
    {
        $this->assertEquals(get_class($this->extension), $this->extension->getName());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::getFunctions
     */
    public function testGetFunctions()
    {
        $this->assertNotEmpty($this->extension->getFunctions());

        foreach ($this->extension->getFunctions() as $function) {
            $this->assertInstanceOf(Twig_SimpleFunction::class, $function);
        }
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::getContentName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::loadContent
     */
    public function testGetContentName()
    {
        $this->mockServices();

        $this->assertEquals('Content name 42', $this->extension->getContentName(42));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::getContentName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::loadContent
     */
    public function testGetContentNameWithException()
    {
        $this->contentServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with($this->equalTo(42))
            ->will($this->throwException(new Exception()));

        $this->assertEquals('', $this->extension->getContentName(42));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::getLocationPath
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::loadLocation
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::loadContent
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
            $this->extension->getLocationPath(22)
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::getLocationPath
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::loadLocation
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::loadContent
     */
    public function testGetLocationPathWithException()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(22))
            ->will($this->throwException(new Exception()));

        $this->assertEquals(array(), $this->extension->getLocationPath(22));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::getContentTypeName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::loadContentType
     */
    public function testGetContentTypeName()
    {
        $this->mockServices();

        $this->assertEquals('Content type some_type', $this->extension->getContentTypeName('some_type'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::getContentTypeName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::loadContentType
     */
    public function testGetContentTypeNameWithException()
    {
        $this->contentTypeServiceMock
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($this->equalTo('some_type'))
            ->will($this->throwException(new Exception()));

        $this->assertEquals('', $this->extension->getContentTypeName('some_type'));
    }
}
