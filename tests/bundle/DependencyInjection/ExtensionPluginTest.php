<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\NetgenLayoutsEzPlatformExtension;

use function array_merge;

final class ExtensionPluginTest extends AbstractExtensionTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::__construct
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\ExtensionPlugin::appendConfigurationFiles
     */
    public function testAppendFromPlugin(): void
    {
        $extension = new NetgenLayoutsExtension();
        $extension->addPlugin(new ExtensionPlugin($this->container, $extension));

        $extension->prepend($this->container);

        $config = array_merge(
            ...$this->container->getExtensionConfig('netgen_layouts'),
        );

        self::assertArrayHasKey('block_type_groups', $config);
        self::assertArrayHasKey('placeholders', $config['block_type_groups']);

        self::assertSame(
            [
                'block_types' => [
                    'ezcontent_field',
                ],
            ],
            $config['block_type_groups']['placeholders'],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new NetgenLayoutsEzPlatformExtension()];
    }
}
