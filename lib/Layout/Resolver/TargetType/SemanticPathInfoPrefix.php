<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Layout\Resolver\TargetType;

final class SemanticPathInfoPrefix extends SemanticPathInfo
{
    public function getType(): string
    {
        return 'ez_semantic_path_info_prefix';
    }
}
