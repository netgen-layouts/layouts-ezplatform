<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Context;

use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Context\Context;
use Netgen\Layouts\Ez\Context\ContextProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ContextProviderTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Netgen\BlockManager\Context\ContextInterface
     */
    private $context;

    /**
     * @var \Netgen\Layouts\Ez\Context\ContextProvider
     */
    private $contextProvider;

    public function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->context = new Context();

        $this->contextProvider = new ContextProvider($this->requestStack);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::__construct
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::provideContext
     */
    public function testProvideContext(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $view = new ContentView();
        $view->setLocation(new Location(['id' => 42]));

        $request->attributes->set('view', $view);

        $this->contextProvider->provideContext($this->context);

        self::assertTrue($this->context->has('ez_location_id'));
        self::assertSame(42, $this->context->get('ez_location_id'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::__construct
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::provideContext
     */
    public function testProvideContextWithInvalidView(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $request->attributes->set('view', new stdClass());

        $this->contextProvider->provideContext($this->context);

        self::assertFalse($this->context->has('ez_location_id'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::__construct
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::provideContext
     */
    public function testProvideContextWithLocation(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $request->attributes->set('location', new Location(['id' => 42]));

        $this->contextProvider->provideContext($this->context);

        self::assertTrue($this->context->has('ez_location_id'));
        self::assertSame(42, $this->context->get('ez_location_id'));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::__construct
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::provideContext
     */
    public function testProvideContextWithInvalidLocation(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $request->attributes->set('location', new stdClass());

        $this->contextProvider->provideContext($this->context);

        self::assertFalse($this->context->has('ez_location_id'));
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
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::__construct
     * @covers \Netgen\Layouts\Ez\Context\ContextProvider::provideContext
     */
    public function testProvideContextWithInvalidRoute(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $request->attributes->set('locationId', 42);
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
