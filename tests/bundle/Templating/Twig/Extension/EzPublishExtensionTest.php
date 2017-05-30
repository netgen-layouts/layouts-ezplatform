<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension;
use PHPUnit\Framework\TestCase;
use Twig_SimpleFunction;

class EzPublishExtensionTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->extension = new EzPublishExtension();
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::getName
     */
    public function testGetName()
    {
        $this->assertEquals(get_class($this->extension), $this->extension->getName());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension\EzPublishExtension::getFunctions
     */
    public function testGetFunctions()
    {
        $this->assertNotEmpty($this->extension->getFunctions());

        foreach ($this->extension->getFunctions() as $function) {
            $this->assertInstanceOf(Twig_SimpleFunction::class, $function);
        }
    }
}
