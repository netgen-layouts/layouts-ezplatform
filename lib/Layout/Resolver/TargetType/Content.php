<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Layout\Resolver\TargetType;

use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use Netgen\Layouts\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\Layouts\Ez\Utils\RemoteIdConverter;
use Netgen\Layouts\Ez\Validator\Constraint as EzConstraints;
use Netgen\Layouts\Layout\Resolver\TargetType;
use Netgen\Layouts\Layout\Resolver\ValueObjectProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class Content extends TargetType implements ValueObjectProviderInterface
{
    private ContentExtractorInterface $contentExtractor;

    private ValueObjectProviderInterface $valueObjectProvider;

    private RemoteIdConverter $remoteIdConverter;

    public function __construct(
        ContentExtractorInterface $contentExtractor,
        ValueObjectProviderInterface $valueObjectProvider,
        RemoteIdConverter $remoteIdConverter
    ) {
        $this->contentExtractor = $contentExtractor;
        $this->valueObjectProvider = $valueObjectProvider;
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

    public function provideValue(Request $request): ?int
    {
        $content = $this->contentExtractor->extractContent($request);

        return $content instanceof APIContent ? (int) $content->id : null;
    }

    public function getValueObject($value): ?object
    {
        return $this->valueObjectProvider->getValueObject($value);
    }

    public function export($value): ?string
    {
        return $this->remoteIdConverter->toContentRemoteId((int) $value);
    }

    public function import($value): int
    {
        return $this->remoteIdConverter->toContentId((string) $value) ?? 0;
    }
}
