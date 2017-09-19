<?php

namespace Netgen\BlockManager\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class ContentType extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.ez_content_type.content_type_not_found';

    /**
     * @var string
     */
    public $notAllowedMessage = 'netgen_block_manager.ez_content_type.content_type_not_allowed';

    /**
     * @var array
     */
    public $allowedTypes = array();

    public function validatedBy()
    {
        return 'ngbm_ez_content_type';
    }
}
