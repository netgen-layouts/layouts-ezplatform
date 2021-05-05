<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\Layouts\Ez\Layout\Resolver\Form\TargetType\Mapper\Location;
use PHPUnit\Framework\TestCase;

final class LocationTest extends TestCase
{
    private Location $mapper;

    protected function setUp(): void
    {
        $this->mapper = new Location();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\TargetType\Mapper\Location::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ContentBrowserType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\TargetType\Mapper\Location::getFormOptions
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
