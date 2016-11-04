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
        $this->assertContainerBuilderHasService('netgen_block_manager.ezpublish.content_provider.request');
        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.ezpublish.content_provider',
            'netgen_block_manager.ezpublish.content_provider.request'
        );
    }
}
