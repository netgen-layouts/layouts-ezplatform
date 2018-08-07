<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\BlockManager\Ez\Layout\Resolver\TargetHandler\Doctrine\Subtree;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\BlockManager\Tests\Layout\Resolver\TargetHandler\Doctrine\AbstractTargetHandlerTest;

final class SubtreeTest extends AbstractTargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetHandler\Doctrine\Subtree::handleQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     */
    public function testMatchRules(): void
    {
        $rules = $this->handler->matchRules($this->getTargetIdentifier(), [1, 2, 42]);

        self::assertCount(1, $rules);
        self::assertSame(8, $rules[0]->id);
    }

    protected function getTargetIdentifier(): string
    {
        return 'ezsubtree';
    }

    protected function getTargetHandler(): TargetHandlerInterface
    {
        return new Subtree();
    }

    protected function insertDatabaseFixtures(string $fixturesPath): void
    {
        parent::insertDatabaseFixtures(__DIR__ . '/../../../../../_fixtures');
    }
}
