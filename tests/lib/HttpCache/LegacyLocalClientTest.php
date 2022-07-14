<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\HttpCache;

use EzSystems\PlatformHttpCacheBundle\RequestAwarePurger;
use Netgen\Layouts\Ez\HttpCache\LegacyLocalClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

use function implode;
use function interface_exists;

final class LegacyLocalClientTest extends TestCase
{
    private MockObject $requestAwarePurgerMock;

    private LegacyLocalClient $client;

    protected function setUp(): void
    {
        if (!interface_exists(RequestAwarePurger::class)) {
            self::markTestSkipped('The tests require eZ Platform HTTP Cache 1.x to run.');
        }

        $this->requestAwarePurgerMock = $this->createMock(RequestAwarePurger::class);
        $this->client = new LegacyLocalClient($this->requestAwarePurgerMock);
    }

    /**
     * @covers \Netgen\Layouts\Ez\HttpCache\LegacyLocalClient::__construct
     * @covers \Netgen\Layouts\Ez\HttpCache\LegacyLocalClient::purge
     */
    public function testPurge(): void
    {
        $tags = ['tag-1', 'tag-2'];

        $purgeRequest = Request::create('http://localhost/', 'PURGE');
        $purgeRequest->headers->set('key', implode(' ', $tags));

        $this->requestAwarePurgerMock
            ->expects(self::once())
            ->method('purgeByRequest')
            ->with(self::equalTo($purgeRequest));

        $this->client->purge($tags);
    }

    /**
     * @covers \Netgen\Layouts\Ez\HttpCache\LegacyLocalClient::__construct
     * @covers \Netgen\Layouts\Ez\HttpCache\LegacyLocalClient::purge
     */
    public function testPurgeWithNoTags(): void
    {
        $this->requestAwarePurgerMock
            ->expects(self::never())
            ->method('purgeByRequest');

        $this->client->purge([]);
    }

    /**
     * @covers \Netgen\Layouts\Ez\HttpCache\LegacyLocalClient::commit
     */
    public function testCommit(): void
    {
        self::assertTrue($this->client->commit());
    }
}
