<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\HttpCache;

use Netgen\Layouts\HttpCache\ClientInterface;
use Toflar\Psr6HttpCacheStore\Psr6StoreInterface;

final class LocalClient implements ClientInterface
{
    private Psr6StoreInterface $cacheStore;

    public function __construct(Psr6StoreInterface $cacheStore)
    {
        $this->cacheStore = $cacheStore;
    }

    public function purge(array $tags): void
    {
        $this->cacheStore->invalidateTags($tags);
    }

    public function commit(): bool
    {
        return true;
    }
}
