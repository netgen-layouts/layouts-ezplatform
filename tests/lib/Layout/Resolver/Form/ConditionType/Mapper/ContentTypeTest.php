<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\ContentType;
use PHPUnit\Framework\TestCase;

final class ContentTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new ContentType();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\ContentType::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertSame(ContentTypeType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\ContentType::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        $this->assertSame(
            [
                'multiple' => true,
            ],
            $this->mapper->getFormOptions()
        );
    }
}
