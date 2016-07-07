<?php

namespace Netgen\BlockManager\Ez\Tests\Persistence\Doctrine\Handler\LayoutResolver\TargetHandler;

use Netgen\BlockManager\Tests\Persistence\Doctrine\Handler\LayoutResolver\TargetHandler\AbstractTargetHandlerTest;
use Netgen\BlockManager\Ez\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler\Content;

class ContentTest extends AbstractTargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     * @covers \Netgen\BlockManager\Ez\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler\Content::handleQuery
     */
    public function testMatchRules()
    {
        $rules = $this->handler->matchRules($this->getTargetIdentifier(), 70);

        self::assertCount(1, $rules);
        self::assertEquals(14, $rules[0]->id);
    }

    /**
     * Returns the target identifier under test.
     *
     * @return string
     */
    protected function getTargetIdentifier()
    {
        return 'ezcontent';
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new Content();
    }
}
