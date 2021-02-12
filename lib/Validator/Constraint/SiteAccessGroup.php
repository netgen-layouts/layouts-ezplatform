<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class SiteAccessGroup extends Constraint
{
    public string $message = 'netgen_layouts.ezplatform.site_access_group.site_access_group_not_found';

    public function validatedBy(): string
    {
        return 'nglayouts_ez_site_access_group';
    }
}
