<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Layout\Resolver\TargetType;

use Ibexa\Contracts\Core\Repository\Values\Content\Location as IbexaLocation;
use Netgen\Layouts\Ibexa\ContentProvider\ContentExtractorInterface;
use Netgen\Layouts\Ibexa\Utils\RemoteIdConverter;
use Netgen\Layouts\Ibexa\Validator\Constraint as IbexaConstraints;
use Netgen\Layouts\Layout\Resolver\TargetType;
use Netgen\Layouts\Layout\Resolver\ValueObjectProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class Location extends TargetType implements ValueObjectProviderInterface
{
    public function __construct(
        private ContentExtractorInterface $contentExtractor,
        private ValueObjectProviderInterface $valueObjectProvider,
        private RemoteIdConverter $remoteIdConverter,
    ) {
    }

    public static function getType(): string
    {
        return 'ibexa_location';
    }

    public function getConstraints(): array
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'numeric']),
            new Constraints\GreaterThanOrEqual(['value' => 0]),
            new IbexaConstraints\Location(['allowInvalid' => true]),
        ];
    }

    public function provideValue(Request $request): ?int
    {
        $location = $this->contentExtractor->extractLocation($request);

        return $location instanceof IbexaLocation ? (int) $location->id : null;
    }

    public function getValueObject(mixed $value): ?object
    {
        return $this->valueObjectProvider->getValueObject($value);
    }

    public function export(mixed $value): ?string
    {
        return $this->remoteIdConverter->toLocationRemoteId((int) $value);
    }

    public function import(mixed $value): ?int
    {
        return $this->remoteIdConverter->toLocationId((string) $value) ?? 0;
    }
}
