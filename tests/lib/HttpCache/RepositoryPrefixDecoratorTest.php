<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\HttpCache;

use Ibexa\HttpCache\RepositoryTagPrefix;
use Netgen\Layouts\HttpCache\ClientInterface;
use Netgen\Layouts\Ibexa\HttpCache\RepositoryPrefixDecorator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RepositoryPrefixDecoratorTest extends TestCase
{
    private MockObject&ClientInterface $clientMock;

    private MockObject&RepositoryTagPrefix $repositoryTagPrefixMock;

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
     * @covers \Netgen\Layouts\Ibexa\HttpCache\RepositoryPrefixDecorator::__construct
     * @covers \Netgen\Layouts\Ibexa\HttpCache\RepositoryPrefixDecorator::purge
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
     * @covers \Netgen\Layouts\Ibexa\HttpCache\RepositoryPrefixDecorator::__construct
     * @covers \Netgen\Layouts\Ibexa\HttpCache\RepositoryPrefixDecorator::commit
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
