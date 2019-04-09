<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class ObjectState extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.ez_object_state.object_state_not_found';

    /**
     * @var string
     */
    public $invalidGroupMessage = 'netgen_block_manager.ez_object_state.object_state_group_not_found';

    /**
     * @var string
     */
    public $notAllowedMessage = 'netgen_block_manager.ez_object_state.object_state_not_allowed';

    /**
     * If not empty, the constraint will validate only if object state identifier
     * is in the list of provided object state identifiers.
     *
     * @var array
     */
    public $allowedStates = [];

    public function validatedBy(): string
    {
        return 'ngbm_ez_object_state';
    }
}
