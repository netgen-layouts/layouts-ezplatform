<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Layout\Resolver\TargetType;

final class SemanticPathInfoPrefix extends SemanticPathInfo
{
    public static function getType(): string
    {
        return 'ibexa_semantic_path_info_prefix';
    }
}
