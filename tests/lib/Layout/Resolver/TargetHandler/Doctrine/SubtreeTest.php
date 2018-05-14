<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\BlockManager\Ez\Layout\Resolver\TargetHandler\Doctrine\Subtree;
use Netgen\BlockManager\Tests\Layout\Resolver\TargetHandler\Doctrine\AbstractTargetHandlerTest;

final class SubtreeTest extends AbstractTargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetHandler\Doctrine\Subtree::handleQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     */
    public function testMatchRules()
    {
        $rules = $this->handler->matchRules($this->getTargetIdentifier(), [1, 2, 42]);

        $this->assertCount(1, $rules);
        $this->assertEquals(8, $rules[0]->id);
    }

    protected function getTargetIdentifier()
    {
        return 'ezsubtree';
    }

    protected function getTargetHandler()
    {
        return new Subtree();
    }

    protected function insertDatabaseFixtures($fixturesPath)
    {
        parent::insertDatabaseFixtures(__DIR__ . '/../../../../../_fixtures');
    }
}
