<?php

namespace Netgen\BlockManager\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class Tag extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.eztags.tag_not_found';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_eztags';
    }
}
