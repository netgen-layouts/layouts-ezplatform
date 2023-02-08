<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\Layouts\Ez\Layout\Resolver\TargetHandler\Doctrine\Location;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\Layout\Resolver\TargetHandler\Doctrine\TargetHandlerTestBase;

final class LocationTest extends TargetHandlerTestBase
{
    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetHandler\Doctrine\Location::handleQuery
     */
    public function testMatchRules(): void
    {
        $rules = $this->handler->matchRules(
            $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED),
            $this->getTargetIdentifier(),
            72,
        );

        self::assertCount(1, $rules);
        self::assertSame(1, $rules[0]->id);
    }

    protected function getTargetIdentifier(): string
    {
        return 'ez_location';
    }

    protected function getTargetHandler(): TargetHandlerInterface
    {
        return new Location();
    }

    protected function insertDatabaseFixtures(string $fixturesPath): void
    {
        parent::insertDatabaseFixtures(__DIR__ . '/../../../../../_fixtures/data.php');
    }
}
