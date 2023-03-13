<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\HttpCache;

use Ibexa\HttpCache\RepositoryTagPrefix;
use Netgen\Layouts\HttpCache\ClientInterface;

use function array_map;

final class RepositoryPrefixDecorator implements ClientInterface
{
    public function __construct(private ClientInterface $innerClient, private RepositoryTagPrefix $prefixService)
    {
    }

    public function purge(array $tags): void
    {
        $prefix = $this->prefixService->getRepositoryPrefix();

        $tags = array_map(
            static fn (string $tag): string => $prefix . $tag,
            $tags,
        );

        $this->innerClient->purge($tags);
    }

    public function commit(): bool
    {
        return $this->innerClient->commit();
    }
}
