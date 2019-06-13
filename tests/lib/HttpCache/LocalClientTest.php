<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\HttpCache;

use EzSystems\PlatformHttpCacheBundle\RequestAwarePurger;
use Netgen\Layouts\Ez\HttpCache\LocalClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class LocalClientTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $requestAwarePurgerMock;

    /**
     * @var \Netgen\Layouts\Ez\HttpCache\LocalClient
     */
    private $client;

    protected function setUp(): void
    {
        $this->requestAwarePurgerMock = $this->createMock(RequestAwarePurger::class);
        $this->client = new LocalClient($this->requestAwarePurgerMock);
    }

    /**
     * @covers \Netgen\Layouts\Ez\HttpCache\LocalClient::__construct
     * @covers \Netgen\Layouts\Ez\HttpCache\LocalClient::purge
     */
    public function testPurge(): void
    {
        $tags = ['tag-1', 'tag-2'];

        $purgeRequest = Request::create('http://localhost/', 'PURGE');
        $purgeRequest->headers->set('key', implode(' ', $tags));

        $this->requestAwarePurgerMock
            ->expects(self::at(0))
            ->method('purgeByRequest')
            ->with(self::equalTo($purgeRequest));

        $this->client->purge($tags);
    }

    /**
     * @covers \Netgen\Layouts\Ez\HttpCache\LocalClient::__construct
     * @covers \Netgen\Layouts\Ez\HttpCache\LocalClient::purge
     */
    public function testPurgeWithNoTags(): void
    {
        $this->requestAwarePurgerMock
            ->expects(self::never())
            ->method('purgeByRequest');

        $this->client->purge([]);
    }

    /**
     * @covers \Netgen\Layouts\Ez\HttpCache\LocalClient::commit
     */
    public function testCommit(): void
    {
        self::assertTrue($this->client->commit());
    }
}
