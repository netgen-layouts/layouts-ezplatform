<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Block\BlockDefinition\Integration\Doctrine;

use Netgen\Layouts\Ibexa\Tests\Block\BlockDefinition\Integration\ComponentTest as BaseComponentTest;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\Layouts\Ibexa\Block\BlockDefinition\Handler\ComponentHandler::buildParameters
 */
final class ComponentTest extends BaseComponentTest
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
