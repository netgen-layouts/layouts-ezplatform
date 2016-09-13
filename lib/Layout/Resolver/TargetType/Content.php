<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\TargetType;

use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
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

        $view = $currentRequest->attributes->get('view');
        if ($view instanceof ContentValueView) {
            $content = $view->getContent();
        } else {
            // @deprecated BC for eZ Publish 5
            $content = $currentRequest->attributes->get('content');
        }

        return $content instanceof APIContent ? $content->id : null;
    }
}
