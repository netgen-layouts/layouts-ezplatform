<?php

namespace Netgen\BlockManager\Ez\Tests\ContentProvider;

use Netgen\BlockManager\Ez\ContentProvider\RequestContentProvider;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use PHPUnit\Framework\TestCase;

class RequestContentProviderTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\RequestContentProvider
     */
    protected $contentProvider;

    public function setUp()
    {
        $this->requestStack = new RequestStack();
        $this->contentProvider = new RequestContentProvider();
        $this->contentProvider->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\RequestContentProvider::provideContent
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

        $contentView = new ContentView();
        $contentView->setContent($content);

        $request = Request::create('/');
        $request->attributes->set('view', $contentView);
        $this->requestStack->push($request);

        $this->assertEquals($content, $this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\RequestContentProvider::provideContent
     */
    public function testProvideContentWithoutContentView()
    {
        $request = Request::create('/');
        $request->attributes->set('view', 42);
        $this->requestStack->push($request);

        $this->assertNull($this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\RequestContentProvider::provideContent
     */
    public function testProvideContentWithoutRequest()
    {
        $this->assertNull($this->contentProvider->provideContent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\RequestContentProvider::provideLocation
     */
    public function testProvideLocation()
    {
        $location = new Location(array('id' => 42));
        $contentView = new ContentView();
        $contentView->setLocation($location);

        $request = Request::create('/');
        $request->attributes->set('view', $contentView);
        $this->requestStack->push($request);

        $this->assertEquals($location, $this->contentProvider->provideLocation());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\RequestContentProvider::provideLocation
     */
    public function testProvideLocationWithoutLocationView()
    {
        $request = Request::create('/');
        $request->attributes->set('view', 42);
        $this->requestStack->push($request);

        $this->assertNull($this->contentProvider->provideLocation());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\RequestContentProvider::provideLocation
     */
    public function testProvideLocationWithoutRequest()
    {
        $this->assertNull($this->contentProvider->provideLocation());
    }
}
