<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\DefaultAppPreviewPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

final class DefaultAppPreviewPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\DefaultAppPreviewPass::addDefaultPreviewRule
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\DefaultAppPreviewPass::process
     */
    public function testProcess(): void
    {
        $this->container->setParameter('ezpublish.siteaccess.list', ['cro']);
        $this->container->setParameter(
            'netgen_block_manager.app.ezplatform.item_preview_template',
            'default.html.twig'
        );

        $this->container->setParameter(
            'ezsettings.default.content_view',
            [
                'full' => [
                    'article' => [
                        'template' => 'article.html.twig',
                    ],
                ],
            ]
        );

        $this->container->setParameter(
            'ezsettings.cro.location_view',
            [
                'full' => [
                    'article' => [
                        'template' => 'article.html.twig',
                    ],
                ],
                'ngbm_app_preview' => [
                    'article' => [
                        'template' => 'ngbm_article.html.twig',
                    ],
                ],
            ]
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'ezsettings.default.content_view',
            [
                'full' => [
                    'article' => [
                        'template' => 'article.html.twig',
                    ],
                ],
                'ngbm_app_preview' => [
                    '___ngbm_app_preview_default___' => [
                        'template' => 'default.html.twig',
                        'match' => [],
                        'params' => [],
                    ],
                ],
            ]
        );

        $this->assertContainerBuilderHasParameter(
            'ezsettings.cro.location_view',
            [
                'full' => [
                    'article' => [
                        'template' => 'article.html.twig',
                    ],
                ],
                'ngbm_app_preview' => [
                    'article' => [
                        'template' => 'ngbm_article.html.twig',
                    ],
                    '___ngbm_app_preview_default___' => [
                        'template' => 'default.html.twig',
                        'match' => [],
                        'params' => [],
                    ],
                ],
            ]
        );

        self::assertFalse($this->container->hasParameter('netgen_block_manager.default.location_view'));
        self::assertFalse($this->container->hasParameter('netgen_block_manager.cro.content_view'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\DefaultAppPreviewPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    /**
     * Register the compiler pass under test.
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DefaultAppPreviewPass());
    }
}
