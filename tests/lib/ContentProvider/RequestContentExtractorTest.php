<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\ContentProvider;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Netgen\Layouts\Ibexa\ContentProvider\RequestContentExtractor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(RequestContentExtractor::class)]
final class RequestContentExtractorTest extends TestCase
{
    private RequestContentExtractor $contentExtractor;

    protected function setUp(): void
    {
        $this->contentExtractor = new RequestContentExtractor();
    }

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

    public function testProvideContentWithoutContentView(): void
    {
        $request = Request::create('/');
        $request->attributes->set('view', 42);

        self::assertNull($this->contentExtractor->extractContent($request));
    }

    public function testProvideLocation(): void
    {
        $location = new Location(['id' => 42]);
        $contentView = new ContentView();
        $contentView->setLocation($location);

        $request = Request::create('/');
        $request->attributes->set('view', $contentView);

        self::assertSame($location, $this->contentExtractor->extractLocation($request));
    }

    public function testProvideLocationWithoutLocationView(): void
    {
        $request = Request::create('/');
        $request->attributes->set('view', 42);

        self::assertNull($this->contentExtractor->extractLocation($request));
    }
}
