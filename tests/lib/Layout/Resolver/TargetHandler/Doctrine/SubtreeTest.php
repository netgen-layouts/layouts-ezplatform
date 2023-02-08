<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\Layouts\Ez\Layout\Resolver\TargetHandler\Doctrine\Subtree;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\Layout\Resolver\TargetHandler\Doctrine\TargetHandlerTestBase;

final class SubtreeTest extends TargetHandlerTestBase
{
    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetHandler\Doctrine\Subtree::handleQuery
     */
    public function testMatchRules(): void
    {
        $rules = $this->handler->matchRules(
            $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED),
            $this->getTargetIdentifier(),
            [1, 2, 42],
        );

        self::assertCount(1, $rules);
        self::assertSame(8, $rules[0]->id);
    }

    protected function getTargetIdentifier(): string
    {
        return 'ez_subtree';
    }

    protected function getTargetHandler(): TargetHandlerInterface
    {
        return new Subtree();
    }

    protected function insertDatabaseFixtures(string $fixturesPath): void
    {
        parent::insertDatabaseFixtures(__DIR__ . '/../../../../../_fixtures/data.php');
    }
}
