<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class SemanticPathInfo implements TargetTypeInterface
{
    use RequestStackAwareTrait;

    /**
     * Returns the target type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'semantic_path_info';
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

        if (!$currentRequest->attributes->has('semanticPathinfo')) {
            return;
        }

        // Semantic path info can in some cases be false (for example, on homepage
        // of a secondary siteaccess: i.e. /cro)
        $semanticPathInfo = $currentRequest->attributes->get('semanticPathinfo');
        if (empty($semanticPathInfo)) {
            $semanticPathInfo = '/';
        }

        return $semanticPathInfo;
    }
}
