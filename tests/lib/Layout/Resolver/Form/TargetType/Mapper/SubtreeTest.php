<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\Layouts\Ez\Layout\Resolver\Form\TargetType\Mapper\Subtree;
use PHPUnit\Framework\TestCase;

final class SubtreeTest extends TestCase
{
    private Subtree $mapper;

    protected function setUp(): void
    {
        $this->mapper = new Subtree();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\TargetType\Mapper\Subtree::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ContentBrowserType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\TargetType\Mapper\Subtree::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        self::assertSame(
            [
                'item_type' => 'ezlocation',
            ],
            $this->mapper->getFormOptions(),
        );
    }
}
