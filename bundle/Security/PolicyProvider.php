<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;

class PolicyProvider implements PolicyProviderInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig(
            [
                'nglayouts' => [
                    'admin' => null,
                    'editor' => null,
                ],
            ]
        );
    }
}
