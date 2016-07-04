<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzPublishSiteAccess;

class SiteAccess implements ConditionTypeInterface
{
    use RequestStackAwareTrait;

    /**
     * @var array
     */
    protected $siteAccessList;

    /**
     * Constructor.
     *
     * @param array $siteAccessList
     */
    public function __construct(array $siteAccessList)
    {
        $this->siteAccessList = $siteAccessList;
    }

    /**
     * Returns the condition type.
     *
     * @return string
     */
    public function getType()
    {
        return 'ezsiteaccess';
    }

    /**
     * Returns the constraints that will be used to validate the condition value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Choice(
                array(
                    'choices' => $this->siteAccessList,
                    'multiple' => true,
                    'strict' => true,
                )
            ),
        );
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
