<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Parameters\ParameterType;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Ibexa\Validator\Constraint as IbexaConstraints;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an ID of a location in Ibexa Platform.
 */
final class LocationType extends ParameterType
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public static function getIdentifier(): string
    {
        return 'ibexa_location';
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
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
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
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
            $location = $this->repository->sudo(
                static fn (Repository $repository): Location => $repository->getLocationService()->loadLocationByRemoteId((string) $value),
            );

            return (int) $location->id;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        $options = $parameterDefinition->getOptions();

        return [
            new Constraints\Type(['type' => 'numeric']),
            new Constraints\GreaterThan(['value' => 0]),
            new IbexaConstraints\Location(
                [
                    'allowInvalid' => $options['allow_invalid'],
                    'allowedTypes' => $options['allowed_types'],
                ],
            ),
        ];
    }
}
