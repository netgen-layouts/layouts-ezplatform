<?php

namespace Netgen\BlockManager\Ez\Parameters\ParameterType;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Parameters\ParameterInterface;
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

    public function getIdentifier()
    {
        return 'ezlocation';
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('allow_invalid', false);
        $optionsResolver->setRequired(array('allow_invalid'));
        $optionsResolver->setAllowedTypes('allow_invalid', array('bool'));
    }

    public function export(ParameterInterface $parameter, $value)
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $this->repository->sudo(
                function (Repository $repository) use ($value) {
                    return $repository->getLocationService()->loadLocation($value);
                }
            );

            return $location->remoteId;
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    public function import(ParameterInterface $parameter, $value)
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $this->repository->sudo(
                function (Repository $repository) use ($value) {
                    return $repository->getLocationService()->loadLocationByRemoteId($value);
                }
            );

            return $location->id;
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    public function isValueEmpty(ParameterInterface $parameter, $value)
    {
        return $value === null;
    }

    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        $options = $parameter->getOptions();

        return array(
            new Constraints\Type(array('type' => 'numeric')),
            new Constraints\GreaterThan(array('value' => 0)),
            new EzConstraints\Location(array('allowInvalid' => $options['allow_invalid'])),
        );
    }
}
