<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Layout\Resolver\TargetType;

use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use Netgen\Layouts\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\Layouts\Ez\Utils\RemoteIdConverter;
use Netgen\Layouts\Ez\Validator\Constraint as EzConstraints;
use Netgen\Layouts\Layout\Resolver\TargetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class Location extends TargetType
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
        return 'ez_location';
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

        return $location instanceof APILocation ? (int) $location->id : null;
    }

    public function export($value): ?string
    {
        return $this->remoteIdConverter->toLocationRemoteId((int) $value);
    }

    public function import($value): ?int
    {
        return $this->remoteIdConverter->toLocationId((string) $value) ?? 0;
    }
}
