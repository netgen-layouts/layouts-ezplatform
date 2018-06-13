<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\BlockManager\Ez\Layout\Resolver\TargetHandler\Doctrine\Location;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\BlockManager\Tests\Layout\Resolver\TargetHandler\Doctrine\AbstractTargetHandlerTest;

final class LocationTest extends AbstractTargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetHandler\Doctrine\Location::handleQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     */
    public function testMatchRules(): void
    {
        $rules = $this->handler->matchRules($this->getTargetIdentifier(), 72);

        $this->assertCount(1, $rules);
        $this->assertEquals(1, $rules[0]->id);
    }

    protected function getTargetIdentifier(): string
    {
        return 'ezlocation';
    }

    protected function getTargetHandler(): TargetHandlerInterface
    {
        return new Location();
    }

    protected function insertDatabaseFixtures(string $fixturesPath): void
    {
        parent::insertDatabaseFixtures(__DIR__ . '/../../../../../_fixtures');
    }
}
