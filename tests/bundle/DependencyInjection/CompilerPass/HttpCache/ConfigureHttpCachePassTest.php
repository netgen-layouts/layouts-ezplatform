<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\CompilerPass\HttpCache;

use eZ\Publish\Core\MVC\Symfony\Cache\Http\FOSPurgeClient;
use eZ\Publish\Core\MVC\Symfony\Cache\Http\LocalPurgeClient;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\ContainerBuilderHasAliasConstraint;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ConfigureHttpCachePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass::process
     *
     * @param string $definitionClass
     * @param bool $fromParameter
     * @param bool $clientEnabled
     *
     * @dataProvider processProvider
     */
    public function testProcess($definitionClass, $fromParameter, $clientEnabled)
    {
        $this->setDefinition('netgen_block_manager.http_cache.client', new Definition());

        if ($fromParameter) {
            $this->setParameter('ezpublish.http_cache.purge_client.class', $definitionClass);
            $definitionClass = '%ezpublish.http_cache.purge_client.class%';
        }

        $this->setDefinition(
            'ezpublish.http_cache.purge_client', new Definition($definitionClass)
        );

        $this->compile();

        $clientEnabled ?
            $this->assertContainerBuilderNotHasAlias('netgen_block_manager.http_cache.client') :
            $this->assertContainerBuilderHasAlias(
                'netgen_block_manager.http_cache.client',
                'netgen_block_manager.http_cache.client.null'
            );
    }

    public function processProvider()
    {
        return array(
            array(FOSPurgeClient::class, false, true),
            array(LocalPurgeClient::class, false, false),
            array(FOSPurgeClient::class, true, true),
            array(LocalPurgeClient::class, true, false),
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertEmpty($this->container->getAliases());
        // The container has at least self ("service_container") as the service
        $this->assertCount(1, $this->container->getServiceIds());
        $this->assertEmpty($this->container->getParameterBag()->all());
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigureHttpCachePass());
    }

    /**
     * Assert that the ContainerBuilder for this test does not have an alias with the given id.
     *
     * @param string $aliasId
     */
    protected function assertContainerBuilderNotHasAlias($aliasId)
    {
        $this->assertThat(
            $this->container,
            $this->logicalNot(
                new ContainerBuilderHasAliasConstraint($aliasId)
            )
        );
    }
}
