<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class Location extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.ezlocation.location_not_found';

    /**
     * If set to true, the constraint will accept values for non existing locations.
     *
     * @var bool
     */
    public $allowInvalid = false;

    public function validatedBy(): string
    {
        return 'ngbm_ezlocation';
    }
}
