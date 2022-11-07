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

final class Subtree extends TargetType implements ValueObjectProviderInterface
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
        return 'ibexa_subtree';
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

    /**
     * @return int[]|null
     */
    public function provideValue(Request $request): ?array
    {
        $location = $this->contentExtractor->extractLocation($request);

        return $location instanceof IbexaLocation ? $location->path : null;
    }

    public function getValueObject($value): ?object
    {
        return $this->valueObjectProvider->getValueObject($value);
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
