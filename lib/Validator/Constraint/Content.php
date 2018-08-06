<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class Content extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.ezcontent.content_not_found';

    /**
     * @var string
     */
    public $typeNotAllowedMessage = 'netgen_block_manager.ezcontent.type_not_allowed';

    /**
     * If set to true, the constraint will accept values for non existing content.
     *
     * @var bool
     */
    public $allowInvalid = false;

    /**
     * If not empty, the constraint will only accept content with provided content types.
     *
     * @var array
     */
    public $allowedTypes = [];

    public function validatedBy(): string
    {
        return 'ngbm_ezcontent';
    }
}
