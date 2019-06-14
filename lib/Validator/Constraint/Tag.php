<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class Tag extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_layouts.ezplatform.tags.tag_not_found';

    /**
     * If set to true, the constraint will accept values for non existing tags.
     *
     * @var bool
     */
    public $allowInvalid = false;

    public function validatedBy(): string
    {
        return 'nglayouts_ez_tags';
    }
}
