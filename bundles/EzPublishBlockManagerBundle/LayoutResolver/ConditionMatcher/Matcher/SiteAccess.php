<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\ConditionMatcher\Matcher;

use Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzPublishSiteAccess;

class SiteAccess implements ConditionMatcherInterface
{
    use RequestStackAwareTrait;

    /**
     * Returns the unique identifier of the condition this matcher matches.
     *
     * @return string
     */
    public function getConditionIdentifier()
    {
        return 'siteaccess';
    }

    /**
     * Returns if this condition matches provided value identifier and values.
     *
     * @param string $valueIdentifier
     * @param array $values
     *
     * @return bool
     */
    public function matches($valueIdentifier, array $values)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        if (empty($values)) {
            return false;
        }

        $siteAccess = $currentRequest->attributes->get('siteaccess');
        if (!$siteAccess instanceof EzPublishSiteAccess) {
            return false;
        }

        return in_array($siteAccess->name, $values);
    }
}
