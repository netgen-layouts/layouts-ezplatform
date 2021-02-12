<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\HttpCache\Varnish;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Layouts\HttpCache\Varnish\HostHeaderProviderInterface;

final class HostHeaderProvider implements HostHeaderProviderInterface
{
    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function provideHostHeader(): string
    {
        return $this->configResolver->getParameter('http_cache.purge_servers')[0];
    }
}
