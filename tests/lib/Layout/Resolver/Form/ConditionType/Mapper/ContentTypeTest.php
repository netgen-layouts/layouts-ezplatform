<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Ibexa\Form\ContentTypeType;
use Netgen\Layouts\Ibexa\Layout\Resolver\Form\ConditionType\Mapper\ContentType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContentType::class)]
final class ContentTypeTest extends TestCase
{
    private ContentType $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ContentType();
    }

    public function testGetFormType(): void
    {
        self::assertSame(ContentTypeType::class, $this->mapper->getFormType());
    }

    public function testGetFormOptions(): void
    {
        self::assertSame(
            [
                'multiple' => true,
            ],
            $this->mapper->getFormOptions(),
        );
    }
}
