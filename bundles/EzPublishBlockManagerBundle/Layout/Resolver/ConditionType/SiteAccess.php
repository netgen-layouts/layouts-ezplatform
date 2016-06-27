<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzPublishSiteAccess;

class SiteAccess implements ConditionTypeInterface
{
    use RequestStackAwareTrait;

    /**
     * Returns the condition type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'ezsiteaccess';
    }

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
