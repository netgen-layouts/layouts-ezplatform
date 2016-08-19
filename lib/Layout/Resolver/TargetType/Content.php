<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\TargetType;

use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class Content implements TargetTypeInterface
{
    use RequestStackAwareTrait;

    /**
     * Returns the target type.
     *
     * @return string
     */
    public function getType()
    {
        return 'ezcontent';
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
            new EzConstraints\Content(),
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

        $attributes = $currentRequest->attributes;
        if ($attributes->get('_route') !== UrlAliasRouter::URL_ALIAS_ROUTE_NAME) {
            return;
        }

        if (!$attributes->has('contentId')) {
            return;
        }

        return $attributes->get('contentId');
    }
}
