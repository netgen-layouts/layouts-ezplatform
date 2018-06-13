<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\CompilerPass\HttpCache;

use eZ\Publish\Core\MVC\Symfony\Cache\Http\FOSPurgeClient;
use eZ\Publish\Core\MVC\Symfony\Cache\Http\LocalPurgeClient;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\ContainerBuilderHasAliasConstraint;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureLegacyHttpCachePass;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

final class ConfigureLegacyHttpCachePassTest extends AbstractCompilerPassTestCase
{
    public function setUp()
    {
        if (!class_exists(FOSPurgeClient::class)) {
            require_once __DIR__ . '/Stubs/LegacyClasses.php';
        }

        parent::setUp();
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureLegacyHttpCachePass::process
     *
     * @param string $definitionClass
     * @param bool $clientEnabled
     *
     * @dataProvider processProvider
     */
    public function testProcess($definitionClass, $clientEnabled)
    {
        $this->setDefinition('netgen_block_manager.http_cache.client', new Definition());
        $this->setDefinition('ezpublish.http_cache.purge_client', new Definition($definitionClass));

        $this->compile();

        $clientEnabled ?
            $this->assertContainerBuilderNotHasAlias('netgen_block_manager.http_cache.client') :
            $this->assertContainerBuilderHasAlias(
                'netgen_block_manager.http_cache.client',
                'netgen_block_manager.http_cache.client.null'
            );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureLegacyHttpCachePass::log
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureLegacyHttpCachePass::process
     */
    public function testProcessWithNoSupportedClient()
    {
        $this->setDefinition('netgen_block_manager.http_cache.client', new Definition());
        $this->setDefinition('ezpublish.http_cache.purge_client', new Definition(stdClass::class));

        $this->compile();

        $this->assertContainerBuilderNotHasAlias('netgen_block_manager.http_cache.client');
    }

    public function processProvider()
    {
        return [
            [FOSPurgeClient::class, true],
            [LocalPurgeClient::class, false],
        ];
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureLegacyHttpCachePass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigureLegacyHttpCachePass());
    }

    /**
     * Assert that the ContainerBuilder for this test does not have an alias with the given id.
     *
     * @param string $aliasId
     */
    private function assertContainerBuilderNotHasAlias($aliasId)
    {
        $this->assertThat(
            $this->container,
            $this->logicalNot(
                new ContainerBuilderHasAliasConstraint($aliasId)
            )
        );
    }
}
