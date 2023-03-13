<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\Layouts\Ibexa\Layout\Resolver\Form\TargetType\Mapper\Content;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Content::class)]
final class ContentTest extends TestCase
{
    private Content $mapper;

    protected function setUp(): void
    {
        $this->mapper = new Content();
    }

    public function testGetFormType(): void
    {
        self::assertSame(ContentBrowserType::class, $this->mapper->getFormType());
    }

    public function testGetFormOptions(): void
    {
        self::assertSame(
            [
                'item_type' => 'ibexa_content',
            ],
            $this->mapper->getFormOptions(),
        );
    }
}
