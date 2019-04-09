<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Block\BlockDefinition\Integration\Doctrine;

use Netgen\Layouts\Ez\Tests\Block\BlockDefinition\Integration\ContentFieldTest as BaseContentFieldTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\Layouts\Ez\Block\BlockDefinition\Handler\ContentFieldHandler::buildParameters
 */
final class ContentFieldTest extends BaseContentFieldTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
