<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Layout\Resolver\ConditionType;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\BlockManager\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class ContentType implements ConditionTypeInterface
{
    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\ContentExtractorInterface
     */
    private $contentExtractor;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeService;

    public function __construct(ContentExtractorInterface $contentExtractor, ContentTypeService $contentTypeService)
    {
        $this->contentExtractor = $contentExtractor;
        $this->contentTypeService = $contentTypeService;
    }

    public function getType(): string
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
                ]
            ),
        ];
    }

    public function matches(Request $request, $value): bool
    {
        if (!is_array($value) || empty($value)) {
            return false;
        }

        $content = $this->contentExtractor->extractContent($request);
        if (!$content instanceof Content) {
            return false;
        }

        $contentType = $this->contentTypeService->loadContentType(
            $content->contentInfo->contentTypeId
        );

        return in_array($contentType->identifier, $value, true);
    }
}
