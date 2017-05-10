<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netgen\BlockManager\Ez\Security\Role;

use Symfony\Component\Security\Core\Role\RoleHierarchy as BaseRoleHierarchy;

class RoleHierarchy extends BaseRoleHierarchy
{
    /**
     * Constructor.
     *
     * @param array $hierarchy An array defining the hierarchy
     */
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
