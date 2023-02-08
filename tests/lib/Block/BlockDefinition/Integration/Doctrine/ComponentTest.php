<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Block\BlockDefinition\Integration\Doctrine;

use Netgen\Layouts\Ez\Tests\Block\BlockDefinition\Integration\ComponentTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\Layouts\Ez\Block\BlockDefinition\Handler\ComponentHandler::buildParameters
 */
final class ComponentTest extends ComponentTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
