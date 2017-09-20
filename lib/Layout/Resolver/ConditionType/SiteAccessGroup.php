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
    private $groupsBySiteAccess;

    public function __construct(array $groupsBySiteAccess)
    {
        $this->groupsBySiteAccess = $groupsBySiteAccess;
    }

    public function getType()
    {
        return 'ez_site_access_group';
    }

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
