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

        $this->assertContainerBuilderHasParameter('netgen_block_manager.default.blocks', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.default.block_groups', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.default.layouts', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.default.block_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.default.layout_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.default.pagelayout',
            'NetgenBlockManagerBundle::pagelayout_empty.html.twig'
        );
    }

    /**
     * We test for existence of one service from each of the config files.
     *
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::load
     */
    public function testServices()
    {
        $this->load();

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.config_resolver');
        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.configuration',
            'netgen_block_manager.configuration.config_resolver'
        );

        $this->assertContainerBuilderHasService('netgen_block_manager.layout_resolver.condition_matcher.siteaccess');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout_resolver.target_builder.location');
        $this->assertContainerBuilderHasService('netgen_block_manager.layout_resolver.rule_handler.doctrine.target_handler.location');
    }
}
