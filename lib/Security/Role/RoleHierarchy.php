<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Security\Role;

use Symfony\Component\Security\Core\Role\RoleHierarchy as BaseRoleHierarchy;

/**
 * This class, in contrast to base RoleHierarchy class, builds the hierarchy
 * from the reversed config provided to the constructor.
 *
 * For example: if admin role includes editor role, and editor role includes
 * API role, this class will make sure that when checking if user has access
 * to API role, both editor and admin roles will pass validation.
 */
final class RoleHierarchy extends BaseRoleHierarchy
{
    /**
     * @param array<string, string[]> $hierarchy
     */
    public function __construct(array $hierarchy)
    {
        $reversedHierarchy = [];
        foreach ($hierarchy as $main => $roles) {
            foreach ($roles as $role) {
                $reversedHierarchy[$role][] = $main;
            }
        }

        parent::__construct($reversedHierarchy);
    }
}
