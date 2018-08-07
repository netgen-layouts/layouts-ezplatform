<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

final class EzPublishExtensionTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension
     */
    private $extension;

    public function setUp(): void
    {
        $this->extension = new EzPublishExtension();
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::getFunctions
     */
    public function testGetFunctions(): void
    {
        self::assertNotEmpty($this->extension->getFunctions());
        self::assertContainsOnlyInstancesOf(TwigFunction::class, $this->extension->getFunctions());
    }
}
