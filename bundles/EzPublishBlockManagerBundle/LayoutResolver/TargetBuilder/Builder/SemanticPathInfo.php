<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder;

use Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\SemanticPathInfo as SemanticPathInfoTarget;
use Symfony\Component\HttpFoundation\Request;

class SemanticPathInfo implements TargetBuilderInterface
{
    use RequestStackAwareTrait;

    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Target
     */
    public function buildTarget()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        if (!$currentRequest->attributes->has('semanticPathinfo')) {
            return false;
        }

        // Semantic path info can in some cases be false (for example, on homepage
        // of Croatian siteaccess: /cro)
        $semanticPathInfo = $currentRequest->attributes->get('semanticPathinfo');
        if (empty($semanticPathInfo)) {
            $semanticPathInfo = '/';
        }

        return new SemanticPathInfoTarget(
            array($semanticPathInfo)
        );
    }
}
