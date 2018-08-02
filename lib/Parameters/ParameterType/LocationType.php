<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Parameters\ParameterType;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an ID of a location in eZ Platform.
 */
final class LocationType extends ParameterType
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public static function getIdentifier(): string
    {
        return 'ezlocation';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefault('allow_invalid', false);
        $optionsResolver->setRequired(['allow_invalid']);
        $optionsResolver->setAllowedTypes('allow_invalid', 'bool');
    }

    public function export(ParameterDefinition $parameterDefinition, $value)
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $this->repository->sudo(
                function (Repository $repository) use ($value): Location {
                    return $repository->getLocationService()->loadLocation($value);
                }
            );

            return $location->remoteId;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function import(ParameterDefinition $parameterDefinition, $value)
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $this->repository->sudo(
                function (Repository $repository) use ($value): Location {
                    return $repository->getLocationService()->loadLocationByRemoteId($value);
                }
            );

            return $location->id;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value): bool
    {
        return $value === null;
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        $options = $parameterDefinition->getOptions();

        return [
            new Constraints\Type(['type' => 'numeric']),
            new Constraints\GreaterThan(['value' => 0]),
            new EzConstraints\Location(['allowInvalid' => $options['allow_invalid']]),
        ];
    }
}
