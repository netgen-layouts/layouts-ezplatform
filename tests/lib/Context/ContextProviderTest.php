<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Context;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Ibexa\Context\ContextProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

#[CoversClass(ContextProvider::class)]
final class ContextProviderTest extends TestCase
{
    private RequestStack $requestStack;

    private Context $context;

    private ContextProvider $contextProvider;

    private MockObject&ContentService $contentServiceMock;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->context = new Context();

        $this->contentServiceMock = $this->createMock(ContentService::class);

        $this->contextProvider = new ContextProvider(
            $this->requestStack,
            $this->contentServiceMock,
            [UrlAliasRouter::URL_ALIAS_ROUTE_NAME],
        );
    }

    public function testProvideContextWithLocationId(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $request->attributes->set('locationId', 42);
        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);

        $this->contextProvider->provideContext($this->context);

        self::assertTrue($this->context->has('ibexa_location_id'));
        self::assertSame(42, $this->context->get('ibexa_location_id'));
    }

    public function testProvideContextWithContentId(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $request->attributes->set('contentId', 42);
        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);

        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContentInfo')
            ->with(self::identicalTo(42))
            ->willReturn(
                new ContentInfo(
                    [
                        'mainLocationId' => 24,
                    ],
                ),
            );

        $this->contextProvider->provideContext($this->context);

        self::assertTrue($this->context->has('ibexa_location_id'));
        self::assertSame(24, $this->context->get('ibexa_location_id'));
    }

    public function testProvideContextWithNoLocationIdAndContentId(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);

        $this->contextProvider->provideContext($this->context);

        self::assertFalse($this->context->has('ibexa_location_id'));
    }

    public function testProvideContextWithInvalidRoute(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $request->attributes->set('_route', 'some_route');

        $this->contextProvider->provideContext($this->context);

        self::assertFalse($this->context->has('ibexa_location_id'));
    }

    public function testProvideContextWithNoRequest(): void
    {
        $this->contextProvider->provideContext($this->context);

        self::assertFalse($this->context->has('ibexa_location_id'));
    }
}
