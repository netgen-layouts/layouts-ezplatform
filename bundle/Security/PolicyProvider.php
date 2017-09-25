<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;

final class PolicyProvider implements PolicyProviderInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig(
            array(
                'nglayouts' => array(
                    'admin' => null,
                    'editor' => null,
                    'api' => null,
                ),
            )
        );
    }
}
