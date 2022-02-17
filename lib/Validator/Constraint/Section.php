<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class Section extends Constraint
{
    public string $message = 'netgen_layouts.ibexa.section.section_not_found';

    public string $notAllowedMessage = 'netgen_layouts.ibexa.section.section_not_allowed';

    /**
     * If not empty, the constraint will validate only if section identifier
     * is in the list of provided section identifiers.
     *
     * @var string[]
     */
    public array $allowedSections = [];

    public function validatedBy(): string
    {
        return 'nglayouts_ibexa_section';
    }
}
