<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class Content extends Constraint
{
    public string $message = 'netgen_layouts.ezplatform.content.content_not_found';

    public string $typeNotAllowedMessage = 'netgen_layouts.ezplatform.content.type_not_allowed';

    /**
     * If set to true, the constraint will accept values for non existing content.
     */
    public bool $allowInvalid = false;

    /**
     * If not empty, the constraint will only accept content with provided content types.
     *
     * @var string[]
     */
    public array $allowedTypes = [];

    public function validatedBy(): string
    {
        return 'nglayouts_ez_content';
    }
}
