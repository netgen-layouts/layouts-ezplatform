<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsEzPlatformBundle\Templating\Twig\Extension\EzPlatformExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

final class EzPlatformExtensionTest extends TestCase
{
    private EzPlatformExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new EzPlatformExtension();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Templating\Twig\Extension\EzPlatformExtension::getFunctions
     */
    public function testGetFunctions(): void
    {
        self::assertNotEmpty($this->extension->getFunctions());
        self::assertContainsOnlyInstancesOf(TwigFunction::class, $this->extension->getFunctions());
    }
}
