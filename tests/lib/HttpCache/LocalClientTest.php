<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\HttpCache;

use EzSystems\PlatformHttpCacheBundle\RequestAwarePurger;
use Netgen\Layouts\Ez\HttpCache\LocalClient;
use PHPUnit\Framework\TestCase;
use Toflar\Psr6HttpCacheStore\Psr6StoreInterface;
use function interface_exists;

final class LocalClientTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheStoreMock;

    /**
     * @var \Netgen\Layouts\Ez\HttpCache\LocalClient
     */
    private $client;

    protected function setUp(): void
    {
        if (interface_exists(RequestAwarePurger::class)) {
            self::markTestSkipped('The tests require eZ Platform HTTP Cache 2.x to run.');
        }

        $this->cacheStoreMock = $this->createMock(Psr6StoreInterface::class);
        $this->client = new LocalClient($this->cacheStoreMock);
    }

    /**
     * @covers \Netgen\Layouts\Ez\HttpCache\LocalClient::__construct
     * @covers \Netgen\Layouts\Ez\HttpCache\LocalClient::purge
     */
    public function testPurge(): void
    {
        $tags = ['tag-1', 'tag-2'];

        $this->cacheStoreMock
            ->expects(self::at(0))
            ->method('invalidateTags')
            ->with(self::identicalTo($tags));

        $this->client->purge($tags);
    }

    /**
     * @covers \Netgen\Layouts\Ez\HttpCache\LocalClient::commit
     */
    public function testCommit(): void
    {
        self::assertTrue($this->client->commit());
    }
}
