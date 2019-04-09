<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class Section extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_layouts.ezplatform.section.section_not_found';

    /**
     * @var string
     */
    public $notAllowedMessage = 'netgen_layouts.ezplatform.section.section_not_allowed';

    /**
     * If not empty, the constraint will validate only if section identifier
     * is in the list of provided section identifiers.
     *
     * @var array
     */
    public $allowedSections = [];

    public function validatedBy(): string
    {
        return 'nglayouts_ez_section';
    }
}
