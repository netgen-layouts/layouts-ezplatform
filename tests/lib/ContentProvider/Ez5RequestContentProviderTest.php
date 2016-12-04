<?php

namespace Netgen\BlockManager\Ez\Tests\ContentProvider;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Ez5RequestContentProviderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationServiceMock;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider
     */
    protected $contentProvider;

    public function setUp()
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->contentProvider = new Ez5RequestContentProvider(
            $this->contentServiceMock,
            $this->locationServiceMock
        );

        $this->requestStack = new RequestStack();
        $this->contentProvider->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::__construct
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::provideContent
     */
    public function testProvideContent()
    {
        $content = new Content(
            array(
                'versionInfo' => new VersionInfo(
                    array(
                        'contentInfo' => new ContentInfo(
                            array(
                                'id' => 42,
                            )
                        ),
                    )
                ),
            )
        );

        $request = Request::create('/');
        $request->attributes->set('content', $content);
        $this->requestStack->push($request);

        $this->contentServiceMock
            ->expects($this->never())
            ->method('loadContent');

        $this->assertEquals($content, $this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::provideContent
     */
    public function testProvideContentWithContentId()
    {
        $content = new Content(
            array(
                'versionInfo' => new VersionInfo(
                    array(
                        'contentInfo' => new ContentInfo(
                            array(
                                'id' => 42,
                            )
                        ),
                    )
                ),
            )
        );

        $request = Request::create('/');
        $request->attributes->set('contentId', 42);
        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);
        $this->requestStack->push($request);

        $this->contentServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with($this->equalTo(42))
            ->will($this->returnValue($content));

        $this->assertEquals($content, $this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::provideContent
     */
    public function testProvideContentWithInvalidContent()
    {
        $request = Request::create('/');
        $request->attributes->set('content', 42);
        $this->requestStack->push($request);

        $this->assertNull($this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::provideContent
     */
    public function testProvideContentWithInvalidRoute()
    {
        $request = Request::create('/');
        $request->attributes->set('contentId', 42);
        $request->attributes->set('_route', 'route');
        $this->requestStack->push($request);

        $this->assertNull($this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::provideContent
     */
    public function testProvideContentWithNonExistentContent()
    {
        $request = Request::create('/');
        $request->attributes->set('contentId', 42);
        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);
        $this->requestStack->push($request);

        $this->contentServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('content', 42)));

        $this->assertNull($this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::provideContent
     */
    public function testProvideContentWithoutRequest()
    {
        $this->assertNull($this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::provideLocation
     */
    public function testProvideLocation()
    {
        $location = new Location(array('id' => 42));

        $request = Request::create('/');
        $request->attributes->set('location', $location);
        $this->requestStack->push($request);

        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->assertEquals($location, $this->contentProvider->provideLocation());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::provideLocation
     */
    public function testProvideLocationWithLocationId()
    {
        $location = new Location(array('id' => 42));

        $request = Request::create('/');
        $request->attributes->set('locationId', 42);
        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);
        $this->requestStack->push($request);

        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->returnValue($location));

        $this->assertEquals($location, $this->contentProvider->provideLocation());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::provideLocation
     */
    public function testProvideLocationWithInvalidLocation()
    {
        $request = Request::create('/');
        $request->attributes->set('location', 42);
        $this->requestStack->push($request);

        $this->assertNull($this->contentProvider->provideLocation());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::provideLocation
     */
    public function testProvideLocationWithInvalidRoute()
    {
        $request = Request::create('/');
        $request->attributes->set('locationId', 42);
        $request->attributes->set('_route', 'route');
        $this->requestStack->push($request);

        $this->assertNull($this->contentProvider->provideLocation());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::provideLocation
     */
    public function testProvideLocationWithNonExistentLocation()
    {
        $request = Request::create('/');
        $request->attributes->set('locationId', 42);
        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);
        $this->requestStack->push($request);

        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('location', 42)));

        $this->assertNull($this->contentProvider->provideLocation());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentProvider::provideLocation
     */
    public function testProvideLocationWithoutRequest()
    {
        $this->assertNull($this->contentProvider->provideLocation());
    }
}
