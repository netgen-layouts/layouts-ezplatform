<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\HttpCache\Varnish;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Layouts\Ez\HttpCache\Varnish\HostHeaderProvider;
use PHPUnit\Framework\TestCase;

final class HostHeaderProviderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $configResolverMock;

    /**
     * @var \Netgen\Layouts\Ez\HttpCache\Varnish\HostHeaderProvider
     */
    private $hostHeaderProvider;

    protected function setUp(): void
    {
        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $this->hostHeaderProvider = new HostHeaderProvider($this->configResolverMock);
    }

    /**
     * @covers \Netgen\Layouts\Ez\HttpCache\Varnish\HostHeaderProvider::__construct
     * @covers \Netgen\Layouts\Ez\HttpCache\Varnish\HostHeaderProvider::provideHostHeader
     */
    public function testProvideHostHeader(): void
    {
        $this->configResolverMock
            ->expects(self::once())
            ->method('getParameter')
            ->with(self::identicalTo('http_cache.purge_servers'))
            ->willReturn(['http://localhost:4242', 'http://localhost:2424']);

        self::assertSame('http://localhost:4242', $this->hostHeaderProvider->provideHostHeader());
    }
}
