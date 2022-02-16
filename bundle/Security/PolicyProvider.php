<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Security;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;

final class PolicyProvider implements PolicyProviderInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder): void
    {
        $configBuilder->addConfig(
            [
                'nglayouts' => [
                    'admin' => null,
                    'editor' => null,
                    'api' => null,
                ],
            ],
        );
    }
}
