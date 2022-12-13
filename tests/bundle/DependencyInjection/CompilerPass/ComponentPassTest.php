<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\ComponentPass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

final class ComponentPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new ComponentPass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\ComponentPass::process
     */
    public function testProcess(): void
    {
        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'ezcomponent_foo' => [
                    'name' => 'Foo Component',
                    'icon' => null,
                    'enabled' => true,
                    'defaults' => [],
                    'definition_identifier' => 'ezcomponent',
                ],
                'test' => [
                    'name' => 'Test',
                    'icon' => null,
                    'enabled' => true,
                    'defaults' => [],
                ],
            ],
        );

        $this->setParameter(
            'netgen_layouts.block_definitions',
            [
                'ezcomponent' => [
                    'name' => 'eZ Component',
                    'icon' => null,
                    'enabled' => true,
                    'defaults' => [],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.ezplatform.block.block_definition_handler.component', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            '__netgen_layouts.ezplatform.block.block_definition_handler.ezcomponent_foo__',
            'netgen_layouts.block_definition_handler',
            ['identifier' => 'ezcomponent_foo'],
        );

        $this->assertContainerBuilderNotHasService('netgen_layouts.ezplatform.block.block_definition_handler.component');

        $this->assertContainerBuilderHasParameter(
            'netgen_layouts.block_types',
            [
                'ezcomponent_foo' => [
                    'name' => 'Foo Component',
                    'icon' => null,
                    'enabled' => true,
                    'defaults' => [],
                ],
                'test' => [
                    'name' => 'Test',
                    'icon' => null,
                    'enabled' => true,
                    'defaults' => [],
                ],
            ],
        );

        $this->assertContainerBuilderHasParameter(
            'netgen_layouts.block_definitions',
            [
                'ezcomponent_foo' => [
                    'name' => 'Foo Component',
                    'icon' => null,
                    'enabled' => true,
                    'defaults' => [],
                ],
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\ComponentPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
