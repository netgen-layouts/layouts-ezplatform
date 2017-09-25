<?php

namespace Netgen\BlockManager\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class SiteAccess extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.ez_site_access.site_access_not_found';

    public function validatedBy()
    {
        return 'ngbm_ez_site_access';
    }
}
