<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function sprintf;

final class ComponentPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (
            !$container->hasParameter('netgen_layouts.block_types')
            || !$container->hasParameter('netgen_layouts.block_definitions')
        ) {
            return;
        }

        /** @var array<string, mixed[]> $blockTypes */
        $blockTypes = $container->getParameter('netgen_layouts.block_types');

        /** @var array<string, mixed[]> $blockDefinitions */
        $blockDefinitions = $container->getParameter('netgen_layouts.block_definitions');

        foreach ($blockTypes as $identifier => $blockType) {
            if (($blockType['definition_identifier'] ?? '') !== 'ezcomponent') {
                continue;
            }

            unset($blockTypes[$identifier]['definition_identifier']);

            $blockDefinitions[$identifier] = [
                'name' => $blockType['name'],
                'icon' => $blockType['icon'],
                'enabled' => $blockType['enabled'],
            ] + $blockDefinitions['ezcomponent'];

            $componentService = clone $container
                ->findDefinition('netgen_layouts.ezplatform.block.block_definition_handler.component');

            $componentService
                ->clearTags()
                ->addTag('netgen_layouts.block_definition_handler', ['identifier' => $identifier]);

            $container->setDefinition(
                sprintf('__netgen_layouts.ezplatform.block.block_definition_handler.%s__', $identifier),
                $componentService,
            );
        }

        unset($blockTypes['ezcomponent'], $blockDefinitions['ezcomponent']);

        $container->removeDefinition('netgen_layouts.ezplatform.block.block_definition_handler.component');

        $container->setParameter('netgen_layouts.block_types', $blockTypes);
        $container->setParameter('netgen_layouts.block_definitions', $blockDefinitions);
    }
}
