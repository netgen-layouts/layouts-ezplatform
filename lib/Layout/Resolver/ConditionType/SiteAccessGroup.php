<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Layout\Resolver\ConditionType;

use Ibexa\Core\MVC\Symfony\SiteAccess as IbexaSiteAccess;
use Netgen\Layouts\Ibexa\Validator\Constraint as IbexaConstraints;
use Netgen\Layouts\Layout\Resolver\ConditionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;
use function array_intersect;
use function count;
use function is_array;

final class SiteAccessGroup extends ConditionType
{
    /**
     * @var array<string, string[]>
     */
    private array $groupsBySiteAccess;

    /**
     * @param array<string, string[]> $groupsBySiteAccess
     */
    public function __construct(array $groupsBySiteAccess)
    {
        $this->groupsBySiteAccess = $groupsBySiteAccess;
    }

    public static function getType(): string
    {
        return 'ibexa_site_access_group';
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
                        new IbexaConstraints\SiteAccessGroup(),
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

        return count(array_intersect($value, $this->groupsBySiteAccess[$siteAccess->name] ?? [])) > 0;
    }
}
