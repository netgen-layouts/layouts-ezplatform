<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension;

final class ExtensionPluginTest extends AbstractExtensionTestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\ExtensionPlugin::appendConfigurationFiles
     */
    public function testAppendFromPlugin()
    {
        $extension = new NetgenBlockManagerExtension();
        $extension->addPlugin(new ExtensionPlugin($this->container));

        $extension->prepend($this->container);

        $config = array_merge(
            ...$this->container->getExtensionConfig('netgen_block_manager')
        );

        $this->assertInternalType('array', $config);

        $this->assertArrayHasKey('block_type_groups', $config);
        $this->assertArrayHasKey('placeholders', $config['block_type_groups']);

        $this->assertEquals(
            [
                'block_types' => [
                    'ezcontent_field',
                ],
            ],
            $config['block_type_groups']['placeholders']
        );
    }

    /**
     * Return an array of container extensions that need to be registered for
     * each test (usually just the container extension you are testing).
     *
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new NetgenEzPublishBlockManagerExtension()];
    }
}
