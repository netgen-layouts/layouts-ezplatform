<?php

namespace Netgen\BlockManager\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class SiteAccess extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.ez_site_access.site_access_not_found';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_ez_site_access';
    }
}
