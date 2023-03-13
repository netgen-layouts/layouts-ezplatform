<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Extension\IbexaExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

#[CoversClass(IbexaExtension::class)]
final class IbexaExtensionTest extends TestCase
{
    private IbexaExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new IbexaExtension();
    }

    public function testGetFunctions(): void
    {
        self::assertNotEmpty($this->extension->getFunctions());
        self::assertContainsOnlyInstancesOf(TwigFunction::class, $this->extension->getFunctions());
    }
}
