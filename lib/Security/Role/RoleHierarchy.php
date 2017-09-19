<?php

namespace Netgen\BlockManager\Ez\Security\Role;

use Symfony\Component\Security\Core\Role\RoleHierarchy as BaseRoleHierarchy;

class RoleHierarchy extends BaseRoleHierarchy
{
    public function __construct(array $hierarchy)
    {
        $reversedHierarchy = array();
        foreach ($hierarchy as $main => $roles) {
            foreach ($roles as $role) {
                $reversedHierarchy[$role][] = $main;
            }
        }

        parent::__construct($reversedHierarchy);
    }
}
