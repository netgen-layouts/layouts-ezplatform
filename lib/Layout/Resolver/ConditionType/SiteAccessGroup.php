<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\ConditionType;

use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzPublishSiteAccess;
use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class SiteAccessGroup implements ConditionTypeInterface
{
    /**
     * @var array
     */
    protected $groupsBySiteAccess;

    /**
     * Constructor.
     *
     * @param array $groupsBySiteAccess
     */
    public function __construct(array $groupsBySiteAccess)
    {
        $this->groupsBySiteAccess = $groupsBySiteAccess;
    }

    /**
     * Returns the condition type.
     *
     * @return string
     */
    public function getType()
    {
        return 'ez_site_access_group';
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
            new Constraints\Type(array('type' => 'array')),
            new Constraints\All(
                array(
                    'constraints' => array(
                        new Constraints\Type(array('type' => 'string')),
                        new EzConstraints\SiteAccessGroup(),
                    ),
                )
            ),
        );
    }

    /**
     * Returns if this request matches the provided value.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $value
     *
     * @return bool
     */
    public function matches(Request $request, $value)
    {
        $siteAccess = $request->attributes->get('siteaccess');
        if (!$siteAccess instanceof EzPublishSiteAccess) {
            return false;
        }

        if (!is_array($value) || empty($value)) {
            return false;
        }

        // We skip the check if siteaccess is not part of any group
        if (!isset($this->groupsBySiteAccess[$siteAccess->name])) {
            return false;
        }

        return !empty(array_intersect($value, $this->groupsBySiteAccess[$siteAccess->name]));
    }
}