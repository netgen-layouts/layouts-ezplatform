<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\HttpCache\Varnish;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Netgen\Layouts\Ibexa\HttpCache\Varnish\HostHeaderProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class HostHeaderProviderTest extends TestCase
{
    private MockObject $configResolverMock;

    private HostHeaderProvider $hostHeaderProvider;

    protected function setUp(): void
    {
        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $this->hostHeaderProvider = new HostHeaderProvider($this->configResolverMock);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\HttpCache\Varnish\HostHeaderProvider::__construct
     * @covers \Netgen\Layouts\Ibexa\HttpCache\Varnish\HostHeaderProvider::provideHostHeader
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
