<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;

class PolicyProvider implements PolicyProviderInterface
{
    /**
     * Adds policies configuration hash to $configBuilder.
     *
     * @param \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface $configBuilder
     */
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig(
            array(
                'nglayouts' => array(
                    'admin' => null,
                    'editor' => null,
                ),
            )
        );
    }
}
