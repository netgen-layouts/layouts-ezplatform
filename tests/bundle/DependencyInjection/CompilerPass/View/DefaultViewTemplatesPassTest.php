<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Tests\DependencyInjection\CompilerPass\View;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

final class DefaultViewTemplatesPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new DefaultViewTemplatesPass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::addDefaultRule
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::process
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::updateRules
     */
    public function testProcess(): void
    {
        $this->container->setParameter('ezpublish.siteaccess.list', ['cro', 'eng']);

        $this->container->setParameter(
            'netgen_layouts.default.view',
            [
                'test_view' => [
                    'app' => [
                        'override_match' => [
                            'template' => 'override_app.html.twig',
                        ],
                    ],
                ],
            ],
        );

        $this->container->setParameter(
            'netgen_layouts.cro.view',
            [
                'test_view' => [
                    'default' => [
                        'override_match' => [
                            'template' => 'override_default.html.twig',
                        ],
                    ],
                ],
            ],
        );

        $this->container->setParameter(
            'netgen_layouts.default_view_templates',
            [
                'test_view' => [
                    'default' => 'default.html.twig',
                    'app' => 'app.html.twig',
                ],
                'other_view' => [
                    'default' => 'default2.html.twig',
                    'app' => 'app2.html.twig',
                ],
            ],
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'netgen_layouts.default.view',
            [
                'test_view' => [
                    'default' => [
                        '___test_view_default_default___' => [
                            'template' => 'default.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                    'app' => [
                        'override_match' => [
                            'template' => 'override_app.html.twig',
                        ],
                        '___test_view_app_default___' => [
                            'template' => 'app.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                ],
                'other_view' => [
                    'default' => [
                        '___other_view_default_default___' => [
                            'template' => 'default2.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                    'app' => [
                        '___other_view_app_default___' => [
                            'template' => 'app2.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                ],
            ],
        );

        $this->assertContainerBuilderHasParameter(
            'netgen_layouts.cro.view',
            [
                'test_view' => [
                    'default' => [
                        'override_match' => [
                            'template' => 'override_default.html.twig',
                        ],
                        '___test_view_default_default___' => [
                            'template' => 'default.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                    'app' => [
                        '___test_view_app_default___' => [
                            'template' => 'app.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                ],
                'other_view' => [
                    'default' => [
                        '___other_view_default_default___' => [
                            'template' => 'default2.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                    'app' => [
                        '___other_view_app_default___' => [
                            'template' => 'app2.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                ],
            ],
        );

        self::assertFalse($this->container->hasParameter('netgen_layouts.eng.view'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
