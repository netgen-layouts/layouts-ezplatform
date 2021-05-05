<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\Layouts\Ez\Layout\Resolver\Form\TargetType\Mapper\Content;
use PHPUnit\Framework\TestCase;

final class ContentTest extends TestCase
{
    private Content $mapper;

    protected function setUp(): void
    {
        $this->mapper = new Content();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\TargetType\Mapper\Content::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ContentBrowserType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\TargetType\Mapper\Content::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        self::assertSame(
            [
                'item_type' => 'ezcontent',
            ],
            $this->mapper->getFormOptions(),
        );
    }
}
