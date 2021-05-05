<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\ContentProvider;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Netgen\Layouts\Ez\ContentProvider\RequestContentExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class RequestContentExtractorTest extends TestCase
{
    private RequestContentExtractor $contentExtractor;

    protected function setUp(): void
    {
        $this->contentExtractor = new RequestContentExtractor();
    }

    /**
     * @covers \Netgen\Layouts\Ez\ContentProvider\RequestContentExtractor::extractContent
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
                            ],
                        ),
                    ],
                ),
            ],
        );

        $contentView = new ContentView();
        $contentView->setContent($content);

        $request = Request::create('/');
        $request->attributes->set('view', $contentView);

        self::assertSame($content, $this->contentExtractor->extractContent($request));
    }

    /**
     * @covers \Netgen\Layouts\Ez\ContentProvider\RequestContentExtractor::extractContent
     */
    public function testProvideContentWithoutContentView(): void
    {
        $request = Request::create('/');
        $request->attributes->set('view', 42);

        self::assertNull($this->contentExtractor->extractContent($request));
    }

    /**
     * @covers \Netgen\Layouts\Ez\ContentProvider\RequestContentExtractor::extractLocation
     */
    public function testProvideLocation(): void
    {
        $location = new Location(['id' => 42]);
        $contentView = new ContentView();
        $contentView->setLocation($location);

        $request = Request::create('/');
        $request->attributes->set('view', $contentView);

        self::assertSame($location, $this->contentExtractor->extractLocation($request));
    }

    /**
     * @covers \Netgen\Layouts\Ez\ContentProvider\RequestContentExtractor::extractLocation
     */
    public function testProvideLocationWithoutLocationView(): void
    {
        $request = Request::create('/');
        $request->attributes->set('view', 42);

        self::assertNull($this->contentExtractor->extractLocation($request));
    }
}
