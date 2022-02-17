<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection\CompilerPass\DefaultAppPreviewPass;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

final class DefaultAppPreviewPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new DefaultAppPreviewPass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection\CompilerPass\DefaultAppPreviewPass::addDefaultPreviewRule
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection\CompilerPass\DefaultAppPreviewPass::process
     */
    public function testProcess(): void
    {
        $this->container->setParameter('ibexa.site_access.list', ['cro']);
        $this->container->setParameter(
            'netgen_layouts.app.ibexa.item_preview_template',
            'default.html.twig',
        );

        $this->container->setParameter(
            'ibexa.site_access.config.default.content_view',
            [
                'full' => [
                    'article' => [
                        'template' => 'article.html.twig',
                    ],
                ],
            ],
        );

        $this->container->setParameter(
            'ibexa.site_access.config.cro.location_view',
            [
                'full' => [
                    'article' => [
                        'template' => 'article.html.twig',
                    ],
                ],
                'nglayouts_app_preview' => [
                    'article' => [
                        'template' => 'nglayouts_article.html.twig',
                    ],
                ],
            ],
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'ibexa.site_access.config.default.content_view',
            [
                'full' => [
                    'article' => [
                        'template' => 'article.html.twig',
                    ],
                ],
                'nglayouts_app_preview' => [
                    '___nglayouts_app_preview_default___' => [
                        'template' => 'default.html.twig',
                        'match' => [],
                        'params' => [],
                    ],
                ],
            ],
        );

        $this->assertContainerBuilderHasParameter(
            'ibexa.site_access.config.cro.location_view',
            [
                'full' => [
                    'article' => [
                        'template' => 'article.html.twig',
                    ],
                ],
                'nglayouts_app_preview' => [
                    'article' => [
                        'template' => 'nglayouts_article.html.twig',
                    ],
                    '___nglayouts_app_preview_default___' => [
                        'template' => 'default.html.twig',
                        'match' => [],
                        'params' => [],
                    ],
                ],
            ],
        );

        self::assertFalse($this->container->hasParameter('netgen_layouts.default.location_view'));
        self::assertFalse($this->container->hasParameter('netgen_layouts.cro.content_view'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\DependencyInjection\CompilerPass\DefaultAppPreviewPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
