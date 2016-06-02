<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionMatcher;

use Netgen\BlockManager\Layout\Resolver\ConditionMatcherInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzPublishSiteAccess;

class SiteAccess implements ConditionMatcherInterface
{
    use RequestStackAwareTrait;

    /**
     * Returns if this condition matches the provided value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function matches($value)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        $siteAccess = $currentRequest->attributes->get('siteaccess');
        if (!$siteAccess instanceof EzPublishSiteAccess) {
            return false;
        }

        if (!is_array($value) || empty($value)) {
            return false;
        }

        return in_array($siteAccess->name, $value);
    }
}
