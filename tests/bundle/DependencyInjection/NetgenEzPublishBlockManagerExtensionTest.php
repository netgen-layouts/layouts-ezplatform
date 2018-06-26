<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension;

final class NetgenEzPublishBlockManagerExtensionTest extends AbstractExtensionTestCase
{
    /**
     * We test for existence of one service from each of the config files.
     *
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::load
     */
    public function testServices(): void
    {
        $this->container->setParameter(
            'kernel.bundles',
            [
                'NetgenTagsBundle' => 'NetgenTagsBundle',
            ]
        );

        $this->load();

        $this->assertContainerBuilderHasService('netgen_block_manager.templating.pagelayout_resolver.ezpublish');
        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.templating.pagelayout_resolver',
            'netgen_block_manager.templating.pagelayout_resolver.ezpublish'
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.config_resolver');
        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.configuration',
            'netgen_block_manager.configuration.config_resolver'
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.target_handler.doctrine.ezlocation');
        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_definition.handler.ezcontent_field');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.condition_type.ez_site_access');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.target_type.ezlocation');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.form.condition_type.mapper.ez_site_access');
        $this->assertContainerBuilderHasService('netgen_block_manager.collection.query_type.handler.ezcontent_search');
        $this->assertContainerBuilderHasService('netgen_block_manager.item.value_loader.ezcontent');
        $this->assertContainerBuilderHasService('netgen_block_manager.form.ez_content_type');
        $this->assertContainerBuilderHasService('netgen_block_manager.parameters.form.mapper.ezlocation');
        $this->assertContainerBuilderHasService('netgen_block_manager.validator.ezlocation');
        $this->assertContainerBuilderHasService('netgen_block_manager.ezpublish.content_provider');
        $this->assertContainerBuilderHasService('netgen_block_manager.context.provider.ezpublish');

        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.locale.provider',
            'netgen_block_manager.locale.provider.ezpublish'
        );
    }

    /**
     * We test for existence of one config value from each of the config files.
     *
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::prepend
     */
    public function testPrepend(): void
    {
        $this->container->setParameter('kernel.bundles', ['NetgenBlockManagerBundle' => true]);
        $this->container->registerExtension(new NetgenBlockManagerExtension());

        /** @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension $extension */
        $extension = $this->container->getExtension('netgen_ez_publish_block_manager');
        $extension->prepend($this->container);

        $config = array_merge_recursive(
            ...$this->container->getExtensionConfig('netgen_block_manager')
        );

        $this->assertArrayHasKey('block_definitions', $config);
        $this->assertArrayHasKey('ezcontent_field', $config['block_definitions']);

        $this->assertArrayHasKey('items', $config);
        $this->assertArrayHasKey('value_types', $config['items']);

        $this->assertArrayHasKey('ezcontent', $config['items']['value_types']);
        $this->assertArrayHasKey('ezlocation', $config['items']['value_types']);

        $this->assertArrayHasKey('query_types', $config);
        $this->assertArrayHasKey('ezcontent_search', $config['query_types']);

        $this->assertArrayHasKey('block_view', $config['view']);
        $this->assertArrayHasKey('default', $config['view']['block_view']);
        $this->assertArrayHasKey('ezcontent_field', $config['view']['block_view']['default']);

        $this->assertArrayHasKey('item_view', $config['view']);
        $this->assertArrayHasKey('default', $config['view']['item_view']);
        $this->assertArrayHasKey('ezcontent', $config['view']['item_view']['default']);

        $this->assertArrayHasKey('rule_condition_view', $config['view']);
        $this->assertArrayHasKey('value', $config['view']['rule_condition_view']);
        $this->assertArrayHasKey('ez_site_access', $config['view']['rule_condition_view']['value']);

        $this->assertArrayHasKey('rule_target_view', $config['view']);
        $this->assertArrayHasKey('value', $config['view']['rule_target_view']);
        $this->assertArrayHasKey('ezchildren', $config['view']['rule_target_view']['value']);
    }

    protected function getContainerExtensions(): array
    {
        return [new NetgenEzPublishBlockManagerExtension()];
    }
}
