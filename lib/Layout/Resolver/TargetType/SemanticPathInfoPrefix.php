<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Layout\Resolver\TargetType;

final class SemanticPathInfoPrefix extends SemanticPathInfo
{
    public static function getType(): string
    {
        return 'ez_semantic_path_info_prefix';
    }
}
