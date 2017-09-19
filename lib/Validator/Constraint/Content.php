<?php

namespace Netgen\BlockManager\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class Content extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.ezcontent.content_not_found';

    public function validatedBy()
    {
        return 'ngbm_ezcontent';
    }
}
