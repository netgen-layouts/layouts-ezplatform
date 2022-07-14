<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Layout\Resolver\ConditionType;

use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\Layouts\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\Layouts\Ez\Validator\Constraint as EzConstraints;
use Netgen\Layouts\Layout\Resolver\ConditionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

use function count;
use function in_array;
use function is_array;

final class ContentType extends ConditionType
{
    private ContentExtractorInterface $contentExtractor;

    public function __construct(ContentExtractorInterface $contentExtractor)
    {
        $this->contentExtractor = $contentExtractor;
    }

    public static function getType(): string
    {
        return 'ez_content_type';
    }

    public function getConstraints(): array
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'array']),
            new Constraints\All(
                [
                    'constraints' => [
                        new Constraints\Type(['type' => 'string']),
                        new EzConstraints\ContentType(),
                    ],
                ],
            ),
        ];
    }

    public function matches(Request $request, $value): bool
    {
        if (!is_array($value) || count($value) === 0) {
            return false;
        }

        $content = $this->contentExtractor->extractContent($request);
        if (!$content instanceof Content) {
            return false;
        }

        return in_array($content->getContentType()->identifier, $value, true);
    }
}
