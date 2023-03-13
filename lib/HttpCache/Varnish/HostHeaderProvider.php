<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\HttpCache\Varnish;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Netgen\Layouts\HttpCache\Varnish\HostHeaderProviderInterface;

final class HostHeaderProvider implements HostHeaderProviderInterface
{
    public function __construct(private ConfigResolverInterface $configResolver)
    {
    }

    public function provideHostHeader(): string
    {
        return $this->configResolver->getParameter('http_cache.purge_servers')[0];
    }
}
