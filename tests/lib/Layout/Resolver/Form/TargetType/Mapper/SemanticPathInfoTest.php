<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\Layouts\Ibexa\Layout\Resolver\Form\TargetType\Mapper\SemanticPathInfo;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class SemanticPathInfoTest extends TestCase
{
    private SemanticPathInfo $mapper;

    protected function setUp(): void
    {
        $this->mapper = new SemanticPathInfo();
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Layout\Resolver\Form\TargetType\Mapper\SemanticPathInfo::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
