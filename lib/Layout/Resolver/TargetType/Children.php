<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Layout\Resolver\TargetType;

use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use Netgen\Layouts\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\Layouts\Ez\Utils\RemoteIdConverter;
use Netgen\Layouts\Ez\Validator\Constraint as EzConstraints;
use Netgen\Layouts\Layout\Resolver\TargetType;
use Netgen\Layouts\Layout\Resolver\ValueObjectProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class Children extends TargetType implements ValueObjectProviderInterface
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
        return 'ez_children';
    }

    public function getConstraints(): array
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'numeric']),
            new Constraints\GreaterThanOrEqual(['value' => 0]),
            new EzConstraints\Location(['allowInvalid' => true]),
        ];
    }

    public function provideValue(Request $request): ?int
    {
        $location = $this->contentExtractor->extractLocation($request);

        return $location instanceof APILocation ? (int) $location->parentLocationId : null;
    }

    public function getValueObject($value): ?object
    {
        return $this->valueObjectProvider->getValueObject($value);
    }

    public function export($value): ?string
    {
        return $this->remoteIdConverter->toLocationRemoteId((int) $value);
    }

    public function import($value): int
    {
        return $this->remoteIdConverter->toLocationId((string) $value) ?? 0;
    }
}
