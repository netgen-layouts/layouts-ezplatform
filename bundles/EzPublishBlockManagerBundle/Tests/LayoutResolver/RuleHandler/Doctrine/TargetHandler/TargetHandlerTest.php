<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler\TargetHandlerTest as BaseTargetHandlerTest;

abstract class TargetHandlerTest extends BaseTargetHandlerTest
{
    /**
     * Sets up the database connection.
     */
    protected function setUp()
    {
        $this->prepareDatabase(
            __DIR__ . '/../../../../../../../lib/Tests/LayoutResolver/RuleHandler/Doctrine/_fixtures/schema',
            __DIR__ . '/../_fixtures'
        );
    }
}
