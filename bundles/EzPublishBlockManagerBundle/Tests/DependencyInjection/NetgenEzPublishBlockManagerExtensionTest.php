<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection;

use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class NetgenEzPublishBlockManagerExtensionTest extends AbstractExtensionTestCase
{
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

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::load
     */
    public function testParameters()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('netgen_block_manager.default.block_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.default.item_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.default.layout_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.default.form_view', array());
    }

    /**
     * We test for existence of one service from each of the config files.
     *
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::load
     */
    public function testServices()
    {
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

        $this->assertContainerBuilderHasService('netgen_block_manager.persistence.doctrine.layout_resolver.query_handler.target_handler.location');
        $this->assertContainerBuilderHasService('netgen_block_manager.block.block_definition.handler.ezcontent_field');
        $this->assertContainerBuilderHasService('netgen_block_manager.event_listener.block_view.ezcontent_field');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.condition_type.siteaccess');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout.resolver.target_type.location');
        $this->assertContainerBuilderHasService('netgen_block_manager.collection.query_type.handler.ezcontent_search');
        $this->assertContainerBuilderHasService('netgen_block_manager.item.value_loader.ezcontent');
        $this->assertContainerBuilderHasService('netgen_block_manager.parameters.parameter_handler.ezlocation');
        $this->assertContainerBuilderHasService('netgen_block_manager.validator.ezlocation');
    }
}
