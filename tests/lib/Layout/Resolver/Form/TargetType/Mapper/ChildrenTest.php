<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\Layouts\Ez\Layout\Resolver\Form\TargetType\Mapper\Children;
use PHPUnit\Framework\TestCase;

final class ChildrenTest extends TestCase
{
    private Children $mapper;

    protected function setUp(): void
    {
        $this->mapper = new Children();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\TargetType\Mapper\Children::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ContentBrowserType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\TargetType\Mapper\Children::getFormOptions
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
