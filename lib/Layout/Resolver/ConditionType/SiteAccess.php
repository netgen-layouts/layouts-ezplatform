<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Layout\Resolver\ConditionType;

use Ibexa\Core\MVC\Symfony\SiteAccess as IbexaSiteAccess;
use Netgen\Layouts\Ibexa\Validator\Constraint as IbexaConstraints;
use Netgen\Layouts\Layout\Resolver\ConditionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;
use function count;
use function in_array;
use function is_array;

final class SiteAccess extends ConditionType
{
    public static function getType(): string
    {
        return 'ibexa_site_access';
    }

    public function getConstraints(): array
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'array']),
            new Constraints\All(
                [
                    'constraints' => [
                        new Constraints\Type(['type' => 'string']),
                        new IbexaConstraints\SiteAccess(),
                    ],
                ],
            ),
        ];
    }

    public function matches(Request $request, $value): bool
    {
        $siteAccess = $request->attributes->get('siteaccess');
        if (!$siteAccess instanceof IbexaSiteAccess) {
            return false;
        }

        if (!is_array($value) || count($value) === 0) {
            return false;
        }

        return in_array($siteAccess->name, $value, true);
    }
}
