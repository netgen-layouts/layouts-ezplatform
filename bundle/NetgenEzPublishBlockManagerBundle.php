<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle;

use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass;
use Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetgenEzPublishBlockManagerBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $configuration = new Configuration();

        /** @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension $blockManagerExtension */
        $blockManagerExtension = $container->getExtension('netgen_block_manager');

        $blockManagerExtension->addConfigTreeBuilder($configuration->getConfigTreeBuilderClosure());
        $blockManagerExtension->addPreProcessor($this->extension->getPreProcessor());
        $blockManagerExtension->addPostProcessor($this->extension->getPostProcessor());
        $blockManagerExtension->addAppendConfigs($this->extension->getAppendConfigs());

        $container->addCompilerPass(new CompilerPass\View\DefaultViewTemplatesPass());
        $container->addCompilerPass(new CompilerPass\DefaultAppPreviewPass());
    }
}
