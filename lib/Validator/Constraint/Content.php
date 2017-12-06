<?php

namespace Netgen\BlockManager\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class Content extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.ezcontent.content_not_found';

    /**
     * If set to true, the constraint will accept values for non existing content.
     *
     * @var bool
     */
    public $allowInvalid = false;

    public function validatedBy()
    {
        return 'ngbm_ezcontent';
    }
}
