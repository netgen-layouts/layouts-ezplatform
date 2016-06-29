<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType;

use Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class Location implements TargetTypeInterface
{
    use RequestStackAwareTrait;

    /**
     * Returns the target type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'ezlocation';
    }

    /**
     * Returns the constraints that will be used to validate the target value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Type(array('type' => 'numeric')),
            new Constraints\GreaterThan(array('value' => 0)),
            new EzConstraints\Location(),
        );
    }

    /**
     * Provides the value for the target to be used in matching process.
     *
     * @return mixed
     */
    public function provideValue()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        if (!$currentRequest->attributes->has('locationId')) {
            return;
        }

        return $currentRequest->attributes->get('locationId');
    }
}
