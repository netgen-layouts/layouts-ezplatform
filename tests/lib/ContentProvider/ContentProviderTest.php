<?php

namespace Netgen\BlockManager\Ez\Tests\ContentProvider;

use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\BlockManager\Ez\ContentProvider\ContentProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ContentProviderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contentExtractorMock;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\ContentProvider
     */
    private $contentProvider;

    public function setUp()
    {
        $this->contentExtractorMock = $this->createMock(ContentExtractorInterface::class);
        $this->requestStack = new RequestStack();

        $this->contentProvider = new ContentProvider(
            $this->contentExtractorMock,
            $this->requestStack
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::__construct
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::provideContent
     */
    public function testProvideContent()
    {
        $content = new Content();

        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->contentExtractorMock
            ->expects($this->any())
            ->method('extractContent')
            ->with($this->equalTo($request))
            ->will($this->returnValue($content));

        $this->assertEquals($content, $this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::provideContent
     */
    public function testProvideContentWithoutContent()
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->contentExtractorMock
            ->expects($this->any())
            ->method('extractContent')
            ->with($this->equalTo($request))
            ->will($this->returnValue(null));

        $this->assertNull($this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::provideContent
     */
    public function testProvideContentWithoutRequest()
    {
        $this->contentExtractorMock
            ->expects($this->never())
            ->method('extractContent');

        $this->assertNull($this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::provideLocation
     */
    public function testProvideLocation()
    {
        $location = new Location();

        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->contentExtractorMock
            ->expects($this->any())
            ->method('extractLocation')
            ->with($this->equalTo($request))
            ->will($this->returnValue($location));

        $this->assertEquals($location, $this->contentProvider->provideLocation());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::provideLocation
     */
    public function testProvideLocationWithoutLocation()
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->contentExtractorMock
            ->expects($this->any())
            ->method('extractLocation')
            ->with($this->equalTo($request))
            ->will($this->returnValue(null));

        $this->assertNull($this->contentProvider->provideLocation());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\ContentProvider::provideLocation
     */
    public function testProvideLocationWithoutRequest()
    {
        $this->contentExtractorMock
            ->expects($this->never())
            ->method('extractLocation');

        $this->assertNull($this->contentProvider->provideLocation());
    }
}
