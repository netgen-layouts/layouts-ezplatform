<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension;

class NetgenEzPublishBlockManagerExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::load
     */
    public function testParameters()
    {
        $this->container->setParameter(
            'kernel.bundles',
            array(
                'NetgenTagsBundle' => 'NetgenTagsBundle',
            )
        );

        $this->load();

        $this->assertContainerBuilderHasParameter('netgen_block_manager.default.view', array());
    }

    /**
     * We test for existence of one service from each of the config files.
     *
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::load
     */
    public function testServices()
    {
        $this->container->setParameter(
            'kernel.bundles',
            array(
                'NetgenTagsBundle' => 'NetgenTagsBundle',
            )
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

        $this->assertContainerBuilderHasService('netgen_block_manager.persistence.doctrine.layout_resolver.query_handler.target_handler.ezlocation');
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
    }

    /**
     * We test for existence of one config value from each of the config files.
     *
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::prepend
     */
    public function testPrepend()
    {
        $this->container->setParameter('kernel.bundles', array('NetgenBlockManagerBundle' => true));
        $this->container->registerExtension(new NetgenBlockManagerExtension());

        $extension = $this->container->getExtension('netgen_ez_publish_block_manager');
        $extension->prepend($this->container);

        $config = array_merge_recursive(
            ...$this->container->getExtensionConfig('netgen_block_manager')
        );

        $this->assertInternalType('array', $config);

        $this->assertArrayHasKey('block_definitions', $config);
        $this->assertArrayHasKey('ezcontent_field', $config['block_definitions']);

        $this->assertArrayHasKey('items', $config);
        $this->assertArrayHasKey('value_types', $config['items']);

        $this->assertArrayHasKey('ezcontent', $config['items']['value_types']);
        $this->assertArrayHasKey('ezlocation', $config['items']['value_types']);

        $this->assertArrayHasKey('query_types', $config);
        $this->assertArrayHasKey('ezcontent_search', $config['query_types']);

        $this->assertArrayHasKey('sources', $config);
        $this->assertArrayHasKey('dynamic', $config['sources']);

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

    /**
     * Return an array of container extensions that need to be registered for
     * each test (usually just the container extension you are testing).
     *
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return array(new NetgenEzPublishBlockManagerExtension());
    }
}
