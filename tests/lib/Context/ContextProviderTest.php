<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Context;

use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Context\Context;
use Netgen\BlockManager\Ez\ContentProvider\RequestContentExtractor;
use Netgen\BlockManager\Ez\Context\ContextProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ContextProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\ContentExtractorInterface
     */
    private $contentExtractor;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Netgen\BlockManager\Context\ContextInterface
     */
    private $context;

    /**
     * @var \Netgen\BlockManager\Ez\Context\ContextProvider
     */
    private $contextProvider;

    public function setUp(): void
    {
        $this->contentExtractor = new RequestContentExtractor();

        $this->requestStack = new RequestStack();
        $this->context = new Context();

        $this->contextProvider = new ContextProvider(
            $this->contentExtractor,
            $this->requestStack
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Context\ContextProvider::__construct
     * @covers \Netgen\BlockManager\Ez\Context\ContextProvider::provideContext
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
     * @covers \Netgen\BlockManager\Ez\Context\ContextProvider::__construct
     * @covers \Netgen\BlockManager\Ez\Context\ContextProvider::provideContext
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
     * @covers \Netgen\BlockManager\Ez\Context\ContextProvider::provideContext
     */
    public function testProvideContextWithNoRequest(): void
    {
        $this->contextProvider->provideContext($this->context);

        self::assertFalse($this->context->has('ez_location_id'));
    }
}
