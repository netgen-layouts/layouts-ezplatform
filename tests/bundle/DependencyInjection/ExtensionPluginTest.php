<?php

declare(strict_types=1);

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
    public function testAppendFromPlugin(): void
    {
        $extension = new NetgenBlockManagerExtension();
        $extension->addPlugin(new ExtensionPlugin($this->container, $extension));

        $extension->prepend($this->container);

        $config = array_merge(
            ...$this->container->getExtensionConfig('netgen_block_manager')
        );

        self::assertArrayHasKey('block_type_groups', $config);
        self::assertArrayHasKey('placeholders', $config['block_type_groups']);

        self::assertSame(
            [
                'block_types' => [
                    'ezcontent_field',
                ],
            ],
            $config['block_type_groups']['placeholders']
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new NetgenEzPublishBlockManagerExtension()];
    }
}
