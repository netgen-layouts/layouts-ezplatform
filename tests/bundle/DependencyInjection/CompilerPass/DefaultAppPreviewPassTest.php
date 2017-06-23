<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\DefaultAppPreviewPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

class DefaultAppPreviewPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\DefaultAppPreviewPass::process
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\DefaultAppPreviewPass::addDefaultPreviewRule
     */
    public function testProcess()
    {
        $this->container->setParameter('ezpublish.siteaccess.list', array('cro'));
        $this->container->setParameter(
            'netgen_block_manager.app.ezpublish.item_preview_template',
            'default.html.twig'
        );

        $this->container->setParameter(
            'ezsettings.default.content_view',
            array(
                'full' => array(
                    'article' => array(
                        'template' => 'article.html.twig',
                    ),
                ),
            )
        );

        $this->container->setParameter(
            'ezsettings.cro.location_view',
            array(
                'full' => array(
                    'article' => array(
                        'template' => 'article.html.twig',
                    ),
                ),
                'ngbm_app_preview' => array(
                    'article' => array(
                        'template' => 'ngbm_article.html.twig',
                    ),
                ),
            )
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'ezsettings.default.content_view',
            array(
                'full' => array(
                    'article' => array(
                        'template' => 'article.html.twig',
                    ),
                ),
                'ngbm_app_preview' => array(
                    '___ngbm_app_preview_default___' => array(
                        'template' => 'default.html.twig',
                        'match' => array(),
                        'params' => array(),
                    ),
                ),
            )
        );

        $this->assertContainerBuilderHasParameter(
            'ezsettings.cro.location_view',
            array(
                'full' => array(
                    'article' => array(
                        'template' => 'article.html.twig',
                    ),
                ),
                'ngbm_app_preview' => array(
                    'article' => array(
                        'template' => 'ngbm_article.html.twig',
                    ),
                    '___ngbm_app_preview_default___' => array(
                        'template' => 'default.html.twig',
                        'match' => array(),
                        'params' => array(),
                    ),
                ),
            )
        );

        $this->assertFalse($this->container->hasParameter('netgen_block_manager.default.location_view'));
        $this->assertFalse($this->container->hasParameter('netgen_block_manager.cro.content_view'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass\DefaultAppPreviewPass::process
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
        $container->addCompilerPass(new DefaultAppPreviewPass());
    }
}
