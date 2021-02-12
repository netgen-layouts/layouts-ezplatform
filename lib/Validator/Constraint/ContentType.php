<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class ContentType extends Constraint
{
    public string $message = 'netgen_layouts.ezplatform.content_type.content_type_not_found';

    public string $notAllowedMessage = 'netgen_layouts.ezplatform.content_type.content_type_not_allowed';

    /**
     * If not empty, the constraint will validate only if content type identifier
     * is in the list of provided content type identifiers.
     *
     * @var array<string, mixed>
     */
    public array $allowedTypes = [];

    public function validatedBy(): string
    {
        return 'nglayouts_ez_content_type';
    }
}
