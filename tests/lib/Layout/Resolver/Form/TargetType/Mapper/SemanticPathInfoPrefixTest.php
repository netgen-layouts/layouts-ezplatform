<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\Layouts\Ibexa\Layout\Resolver\Form\TargetType\Mapper\SemanticPathInfoPrefix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[CoversClass(SemanticPathInfoPrefix::class)]
final class SemanticPathInfoPrefixTest extends TestCase
{
    private SemanticPathInfoPrefix $mapper;

    protected function setUp(): void
    {
        $this->mapper = new SemanticPathInfoPrefix();
    }

    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
