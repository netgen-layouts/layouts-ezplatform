<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Block\BlockDefinition\Integration\Doctrine;

use Netgen\BlockManager\Ez\Tests\Block\BlockDefinition\Integration\ContentFieldTest as BaseContentFieldTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

/**
 * @covers \Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler::buildParameters
 */
final class ContentFieldTest extends BaseContentFieldTest
{
    use TestCaseTrait;

    public function tearDown()
    {
        $this->closeDatabase();
    }

    /**
     * Prepares the persistence handler used in tests.
     */
    public function preparePersistence()
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }
}
