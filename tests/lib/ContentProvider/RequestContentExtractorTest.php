<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\ContentProvider;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Netgen\BlockManager\Ez\ContentProvider\RequestContentExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class RequestContentExtractorTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\RequestContentExtractor
     */
    private $contentExtractor;

    public function setUp()
    {
        $this->contentExtractor = new RequestContentExtractor();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\RequestContentExtractor::extractContent
     */
    public function testProvideContent()
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

        $contentView = new ContentView();
        $contentView->setContent($content);

        $request = Request::create('/');
        $request->attributes->set('view', $contentView);

        $this->assertEquals($content, $this->contentExtractor->extractContent($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\RequestContentExtractor::extractContent
     */
    public function testProvideContentWithoutContentView()
    {
        $request = Request::create('/');
        $request->attributes->set('view', 42);

        $this->assertNull($this->contentExtractor->extractContent($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\RequestContentExtractor::extractLocation
     */
    public function testProvideLocation()
    {
        $location = new Location(['id' => 42]);
        $contentView = new ContentView();
        $contentView->setLocation($location);

        $request = Request::create('/');
        $request->attributes->set('view', $contentView);

        $this->assertEquals($location, $this->contentExtractor->extractLocation($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\ContentProvider\RequestContentExtractor::extractLocation
     */
    public function testProvideLocationWithoutLocationView()
    {
        $request = Request::create('/');
        $request->attributes->set('view', 42);

        $this->assertNull($this->contentExtractor->extractLocation($request));
    }
}
