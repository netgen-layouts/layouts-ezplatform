<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class SemanticPathInfo implements TargetTypeInterface
{
    public function getType()
    {
        return 'ez_semantic_path_info';
    }

    public function getConstraints()
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'string']),
        ];
    }

    public function provideValue(Request $request)
    {
        if (!$request->attributes->has('semanticPathinfo')) {
            return;
        }

        // Semantic path info can in some cases be false (for example, on homepage
        // of a secondary siteaccess: i.e. /cro)
        $semanticPathInfo = $request->attributes->get('semanticPathinfo');
        if (empty($semanticPathInfo)) {
            $semanticPathInfo = '/';
        }

        return $semanticPathInfo;
    }
}
