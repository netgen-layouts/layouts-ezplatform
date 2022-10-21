<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Parameters\ParameterType;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Layouts\Ez\Validator\Constraint as EzConstraints;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\ValueObjectProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an ID of a location in eZ Platform.
 */
final class LocationType extends ParameterType implements ValueObjectProviderInterface
{
    private Repository $repository;

    private ValueObjectProviderInterface $valueObjectProvider;

    public function __construct(Repository $repository, ValueObjectProviderInterface $valueObjectProvider)
    {
        $this->repository = $repository;
        $this->valueObjectProvider = $valueObjectProvider;
    }

    public static function getIdentifier(): string
    {
        return 'ez_location';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired(['allow_invalid', 'allowed_types']);

        $optionsResolver->setDefault('allow_invalid', false);
        $optionsResolver->setDefault('allowed_types', []);

        $optionsResolver->setAllowedTypes('allow_invalid', 'bool');
        $optionsResolver->setAllowedTypes('allowed_types', 'string[]');
    }

    public function fromHash(ParameterDefinition $parameterDefinition, $value): ?int
    {
        return $value !== null ? (int) $value : null;
    }

    public function export(ParameterDefinition $parameterDefinition, $value): ?string
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $this->repository->sudo(
                static fn (Repository $repository): Location => $repository->getLocationService()->loadLocation((int) $value),
            );

            return $location->remoteId;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function import(ParameterDefinition $parameterDefinition, $value): ?int
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $this->repository->sudo(
                static fn (Repository $repository): Location => $repository->getLocationService()->loadLocationByRemoteId((string) $value),
            );

            return (int) $location->id;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function getValueObject($value): ?object
    {
        return $this->valueObjectProvider->getValueObject($value);
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        $options = $parameterDefinition->getOptions();

        return [
            new Constraints\Type(['type' => 'numeric']),
            new Constraints\GreaterThan(['value' => 0]),
            new EzConstraints\Location(
                [
                    'allowInvalid' => $options['allow_invalid'],
                    'allowedTypes' => $options['allowed_types'],
                ],
            ),
        ];
    }
}
