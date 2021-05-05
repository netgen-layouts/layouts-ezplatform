<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\HttpCache;

use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;
use Netgen\Layouts\Ez\HttpCache\RepositoryPrefixDecorator;
use Netgen\Layouts\HttpCache\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RepositoryPrefixDecoratorTest extends TestCase
{
    private MockObject $clientMock;

    private MockObject $repositoryTagPrefixMock;

    private RepositoryPrefixDecorator $repositoryPrefixDecorator;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->repositoryTagPrefixMock = $this->createMock(RepositoryTagPrefix::class);

        $this->repositoryPrefixDecorator = new RepositoryPrefixDecorator(
            $this->clientMock,
            $this->repositoryTagPrefixMock,
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\HttpCache\RepositoryPrefixDecorator::__construct
     * @covers \Netgen\Layouts\Ez\HttpCache\RepositoryPrefixDecorator::purge
     */
    public function testPurge(): void
    {
        $this->repositoryTagPrefixMock
            ->expects(self::once())
            ->method('getRepositoryPrefix')
            ->willReturn('prefix_');

        $this->clientMock
            ->expects(self::once())
            ->method('purge')
            ->with(self::identicalTo(['prefix_tag-1', 'prefix_tag-2']));

        $this->repositoryPrefixDecorator->purge(['tag-1', 'tag-2']);
    }

    /**
     * @covers \Netgen\Layouts\Ez\HttpCache\RepositoryPrefixDecorator::__construct
     * @covers \Netgen\Layouts\Ez\HttpCache\RepositoryPrefixDecorator::commit
     */
    public function testCommit(): void
    {
        $this->clientMock
            ->expects(self::once())
            ->method('commit')
            ->willReturn(true);

        self::assertTrue($this->repositoryPrefixDecorator->commit());
    }
}
