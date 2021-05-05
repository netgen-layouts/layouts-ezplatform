<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Context;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Ez\Context\ContextProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ContextProviderTest extends TestCase
{
    private RequestStack $requestStack;

    private Context $context;

    private ContextProvider $contextProvider;

    private MockObject $contentServiceMock;

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

    /**
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::__construct
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::provideContext
     */
    public function testProvideContextWithLocationId(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $request->attributes->set('locationId', 42);
        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);

        $this->contextProvider->provideContext($this->context);

        self::assertTrue($this->context->has('ez_location_id'));
        self::assertSame(42, $this->context->get('ez_location_id'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::provideContext
     */
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

        self::assertTrue($this->context->has('ez_location_id'));
        self::assertSame(24, $this->context->get('ez_location_id'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::provideContext
     */
    public function testProvideContextWithNoLocationIdAndContentId(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);

        $this->contextProvider->provideContext($this->context);

        self::assertFalse($this->context->has('ez_location_id'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::provideContext
     */
    public function testProvideContextWithInvalidRoute(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $request->attributes->set('_route', 'some_route');

        $this->contextProvider->provideContext($this->context);

        self::assertFalse($this->context->has('ez_location_id'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::provideContext
     */
    public function testProvideContextWithNoRequest(): void
    {
        $this->contextProvider->provideContext($this->context);

        self::assertFalse($this->context->has('ez_location_id'));
    }
}
