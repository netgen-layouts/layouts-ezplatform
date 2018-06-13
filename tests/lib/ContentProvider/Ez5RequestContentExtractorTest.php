<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\ContentProvider;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class Ez5RequestContentExtractorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $locationServiceMock;

    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor
     */
    private $contentProvider;

    public function setUp(): void
    {
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->locationServiceMock = $this->createMock(LocationService::class);

        $this->contentProvider = new Ez5RequestContentExtractor(
            $this->contentServiceMock,
            $this->locationServiceMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor::__construct
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor::extractContent
     */
    public function testProvideContent(): void
    {
        $content = new Content(
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
        $request->attributes->set('content', $content);

        $this->contentServiceMock
            ->expects($this->never())
            ->method('loadContent');

        $this->assertEquals($content, $this->contentProvider->extractContent($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor::extractContent
     */
    public function testProvideContentWithContentId(): void
    {
        $content = new Content(
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
        $request->attributes->set('contentId', 42);
        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);

        $this->contentServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with($this->equalTo(42))
            ->will($this->returnValue($content));

        $this->assertEquals($content, $this->contentProvider->extractContent($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor::extractContent
     */
    public function testProvideContentWithInvalidContent(): void
    {
        $request = Request::create('/');
        $request->attributes->set('content', 42);

        $this->assertNull($this->contentProvider->extractContent($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor::extractContent
     */
    public function testProvideContentWithInvalidRoute(): void
    {
        $request = Request::create('/');
        $request->attributes->set('contentId', 42);
        $request->attributes->set('_route', 'route');

        $this->assertNull($this->contentProvider->extractContent($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor::extractContent
     */
    public function testProvideContentWithNonExistentContent(): void
    {
        $request = Request::create('/');
        $request->attributes->set('contentId', 42);
        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);

        $this->contentServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('content', 42)));

        $this->assertNull($this->contentProvider->extractContent($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor::extractLocation
     */
    public function testProvideLocation(): void
    {
        $location = new Location(['id' => 42]);

        $request = Request::create('/');
        $request->attributes->set('location', $location);

        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->assertEquals($location, $this->contentProvider->extractLocation($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor::extractLocation
     */
    public function testProvideLocationWithLocationId(): void
    {
        $location = new Location(['id' => 42]);

        $request = Request::create('/');
        $request->attributes->set('locationId', 42);
        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);

        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->returnValue($location));

        $this->assertEquals($location, $this->contentProvider->extractLocation($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor::extractLocation
     */
    public function testProvideLocationWithInvalidLocation(): void
    {
        $request = Request::create('/');
        $request->attributes->set('location', 42);

        $this->assertNull($this->contentProvider->extractLocation($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor::extractLocation
     */
    public function testProvideLocationWithInvalidRoute(): void
    {
        $request = Request::create('/');
        $request->attributes->set('locationId', 42);
        $request->attributes->set('_route', 'route');

        $this->assertNull($this->contentProvider->extractLocation($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\Ez5RequestContentExtractor::extractLocation
     */
    public function testProvideLocationWithNonExistentLocation(): void
    {
        $request = Request::create('/');
        $request->attributes->set('locationId', 42);
        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);

        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('location', 42)));

        $this->assertNull($this->contentProvider->extractLocation($request));
    }
}
