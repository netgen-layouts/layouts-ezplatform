<?php

namespace Netgen\BlockManager\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class SiteAccessGroup extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.ez_site_access_group.site_access_group_not_found';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_ez_site_access_group';
    }
}
