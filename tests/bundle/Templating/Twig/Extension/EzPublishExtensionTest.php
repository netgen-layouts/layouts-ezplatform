<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class EzPublishExtensionTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension
     */
    private $extension;

    public function setUp()
    {
        $this->extension = new EzPublishExtension();
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::getFunctions
     */
    public function testGetFunctions()
    {
        $this->assertNotEmpty($this->extension->getFunctions());

        foreach ($this->extension->getFunctions() as $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
        }
    }
}
