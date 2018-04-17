<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\ConditionType;

use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzPublishSiteAccess;
use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class SiteAccess implements ConditionTypeInterface
{
    public function getType()
    {
        return 'ez_site_access';
    }

    public function getConstraints()
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'array']),
            new Constraints\All(
                [
                    'constraints' => [
                        new Constraints\Type(['type' => 'string']),
                        new EzConstraints\SiteAccess(),
                    ],
                ]
            ),
        ];
    }

    public function matches(Request $request, $value)
    {
        $siteAccess = $request->attributes->get('siteaccess');
        if (!$siteAccess instanceof EzPublishSiteAccess) {
            return false;
        }

        if (!is_array($value) || empty($value)) {
            return false;
        }

        return in_array($siteAccess->name, $value, true);
    }
}
