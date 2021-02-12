<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Layout\Resolver\TargetType;

use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use Netgen\Layouts\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\Layouts\Ez\Utils\RemoteIdConverter;
use Netgen\Layouts\Ez\Validator\Constraint as EzConstraints;
use Netgen\Layouts\Layout\Resolver\TargetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class Content extends TargetType
{
    private ContentExtractorInterface $contentExtractor;

    private RemoteIdConverter $remoteIdConverter;

    public function __construct(ContentExtractorInterface $contentExtractor, RemoteIdConverter $remoteIdConverter)
    {
        $this->contentExtractor = $contentExtractor;
        $this->remoteIdConverter = $remoteIdConverter;
    }

    public static function getType(): string
    {
        return 'ez_content';
    }

    public function getConstraints(): array
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'numeric']),
            new Constraints\GreaterThanOrEqual(['value' => 0]),
            new EzConstraints\Content(['allowInvalid' => true]),
        ];
    }

    public function provideValue(Request $request)
    {
        $content = $this->contentExtractor->extractContent($request);

        return $content instanceof APIContent ? $content->id : null;
    }

    public function export($value)
    {
        return $this->remoteIdConverter->toContentRemoteId((int) $value);
    }

    public function import($value)
    {
        return $this->remoteIdConverter->toContentId((string) $value) ?? 0;
    }
}
