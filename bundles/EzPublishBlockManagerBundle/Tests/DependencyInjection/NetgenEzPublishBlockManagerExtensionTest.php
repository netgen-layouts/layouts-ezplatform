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
        return array(
            new NetgenEzPublishBlockManagerExtension(),
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\NetgenEzPublishBlockManagerExtension::load
     */
    public function testLoad()
    {
        $this->load();

        $this->assertContainerBuilderHasService('netgen_block_manager.configuration.config_resolver');
        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.configuration',
            'netgen_block_manager.configuration.config_resolver'
        );
    }
}
