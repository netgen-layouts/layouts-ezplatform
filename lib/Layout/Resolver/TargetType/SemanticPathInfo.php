<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

use function is_string;

class SemanticPathInfo extends TargetType
{
    public static function getType(): string
    {
        return 'ez_semantic_path_info';
    }

    public function getConstraints(): array
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'string']),
        ];
    }

    public function provideValue(Request $request): ?string
    {
        if (!$request->attributes->has('semanticPathinfo')) {
            return null;
        }

        // Semantic path info can in some cases be false (for example, on homepage
        // of a secondary siteaccess: i.e. /cro)
        $semanticPathInfo = $request->attributes->get('semanticPathinfo');
        if (!is_string($semanticPathInfo) || $semanticPathInfo === '') {
            $semanticPathInfo = '/';
        }

        return $semanticPathInfo;
    }
}
