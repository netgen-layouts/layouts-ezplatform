<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Ez\Form\ContentTypeType;
use Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper\ContentType;
use PHPUnit\Framework\TestCase;

final class ContentTypeTest extends TestCase
{
    private ContentType $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ContentType();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper\ContentType::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ContentTypeType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper\ContentType::getFormOptions
     */
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
