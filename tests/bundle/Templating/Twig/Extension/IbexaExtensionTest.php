<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Extension\IbexaExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

final class IbexaExtensionTest extends TestCase
{
    private IbexaExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new IbexaExtension();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Extension\IbexaExtension::getFunctions
     */
    public function testGetFunctions(): void
    {
        self::assertNotEmpty($this->extension->getFunctions());
        self::assertContainsOnlyInstancesOf(TwigFunction::class, $this->extension->getFunctions());
    }
}
