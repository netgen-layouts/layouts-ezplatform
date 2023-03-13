<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Block\BlockDefinition\Integration\Doctrine;

use Netgen\Layouts\Ibexa\Block\BlockDefinition\Handler\ContentFieldHandler;
use Netgen\Layouts\Ibexa\Tests\Block\BlockDefinition\Integration\ContentFieldTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ContentFieldHandler::class)]
final class ContentFieldTest extends ContentFieldTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
