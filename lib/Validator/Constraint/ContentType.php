<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class ContentType extends Constraint
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
     * If not empty, the constraint will validate only if content type identifier
     * is in the list of provided content type identifiers.
     *
     * @var array
     */
    public $allowedTypes = [];

    public function validatedBy(): string
    {
        return 'ngbm_ez_content_type';
    }
}
