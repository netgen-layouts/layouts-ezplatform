<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection\CompilerPass\ComponentPass;
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
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection\CompilerPass\ComponentPass::process
     */
    public function testProcess(): void
    {
        $this->setParameter(
            'netgen_layouts.block_types',
            [
                'ibexa_component_foo' => [
                    'name' => 'Foo Component',
                    'icon' => null,
                    'enabled' => true,
                    'defaults' => [],
                    'definition_identifier' => 'ibexa_component',
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
                'ibexa_component' => [
                    'name' => 'Ibexa Component',
                    'icon' => null,
                    'enabled' => true,
                    'defaults' => [],
                ],
            ],
        );

        $this->setDefinition('netgen_layouts.ibexa.block.block_definition_handler.component', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            '__netgen_layouts.ibexa.block.block_definition_handler.ibexa_component_foo__',
            'netgen_layouts.block_definition_handler',
            ['identifier' => 'ibexa_component_foo'],
        );

        $this->assertContainerBuilderNotHasService('netgen_layouts.ibexa.block.block_definition_handler.component');

        $this->assertContainerBuilderHasParameter(
            'netgen_layouts.block_types',
            [
                'ibexa_component_foo' => [
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
                'ibexa_component_foo' => [
                    'name' => 'Foo Component',
                    'icon' => null,
                    'enabled' => true,
                    'defaults' => [],
                ],
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection\CompilerPass\ComponentPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
