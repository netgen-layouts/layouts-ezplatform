<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class ObjectState extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_layouts.ezplatform.object_state.object_state_not_found';

    /**
     * @var string
     */
    public $invalidGroupMessage = 'netgen_layouts.ezplatform.object_state.object_state_group_not_found';

    /**
     * @var string
     */
    public $notAllowedMessage = 'netgen_layouts.ezplatform.object_state.object_state_not_allowed';

    /**
     * If not empty, the constraint will validate only if object state identifier
     * is in the list of provided object state identifiers.
     *
     * @var array<string, mixed>
     */
    public $allowedStates = [];

    public function validatedBy(): string
    {
        return 'nglayouts_ez_object_state';
    }
}
