<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Block\BlockDefinition\Integration\Doctrine;

use Netgen\Layouts\Ez\Tests\Block\BlockDefinition\Integration\ContentFieldTest as BaseContentFieldTest;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\Layouts\Ez\Block\BlockDefinition\Handler\ContentFieldHandler::buildParameters
 */
final class ContentFieldTest extends BaseContentFieldTest
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
