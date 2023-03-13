<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Tests\DependencyInjection\CompilerPass\HttpCache;

use Ibexa\HttpCache\PurgeClient\LocalPurgeClient;
use Ibexa\HttpCache\PurgeClient\VarnishPurgeClient;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\ContainerBuilderHasAliasConstraint;
use Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

#[CoversClass(ConfigureHttpCachePass::class)]
final class ConfigureHttpCachePassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new ConfigureHttpCachePass());
    }

    #[DataProvider('processDataProvider')]
    public function testProcess(string $definitionClass, bool $clientEnabled): void
    {
        $this->setDefinition('netgen_layouts.http_cache.client', new Definition());
        $this->setDefinition('ibexa.http_cache.purge_client_internal', new Definition($definitionClass));

        $this->compile();

        $clientEnabled ?
            $this->assertContainerBuilderNotHasAlias('netgen_layouts.http_cache.client') :
            $this->assertContainerBuilderHasAlias(
                'netgen_layouts.http_cache.client',
                'netgen_layouts.ibexa.http_cache.client.local',
            );
    }

    public function testProcessWithNoSupportedClient(): void
    {
        $this->setDefinition('netgen_layouts.http_cache.client', new Definition());
        $this->setDefinition('ibexa.http_cache.purge_client_internal', new Definition(stdClass::class));

        $this->compile();

        $this->assertContainerBuilderNotHasAlias('netgen_layouts.http_cache.client');
    }

    public static function processDataProvider(): array
    {
        return [
            [VarnishPurgeClient::class, true],
            [LocalPurgeClient::class, false],
        ];
    }

    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    /**
     * Assert that the ContainerBuilder for this test does not have an alias with the given id.
     */
    private function assertContainerBuilderNotHasAlias(string $aliasId): void
    {
        self::assertThat(
            $this->container,
            self::logicalNot(
                new ContainerBuilderHasAliasConstraint($aliasId),
            ),
        );
    }
}
