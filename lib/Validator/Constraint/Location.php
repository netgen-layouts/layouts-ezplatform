<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class Location extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.ezlocation.location_not_found';

    /**
     * @var string
     */
    public $typeNotAllowedMessage = 'netgen_block_manager.ezlocation.type_not_allowed';

    /**
     * If set to true, the constraint will accept values for non existing locations.
     *
     * @var bool
     */
    public $allowInvalid = false;

    /**
     * If not empty, the constraint will only accept locations with provided content types.
     *
     * @var array
     */
    public $allowedTypes = [];

    public function validatedBy(): string
    {
        return 'ngbm_ezlocation';
    }
}
