<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\BlockManager\Ez\Layout\Resolver\TargetHandler\Doctrine\Location;
use Netgen\BlockManager\Tests\Layout\Resolver\TargetHandler\Doctrine\AbstractTargetHandlerTest;

final class LocationTest extends AbstractTargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetHandler\Doctrine\Location::handleQuery
     */
    public function testMatchRules()
    {
        $rules = $this->handler->matchRules($this->getTargetIdentifier(), 72);

        $this->assertCount(1, $rules);
        $this->assertEquals(11, $rules[0]->id);
    }

    /**
     * Returns the target identifier under test.
     *
     * @return string
     */
    protected function getTargetIdentifier()
    {
        return 'ezlocation';
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\Ez\Layout\Resolver\TargetHandler\Doctrine\Location
     */
    protected function getTargetHandler()
    {
        return new Location();
    }
}
