<?php

namespace Netgen\BlockManager\Ez\Tests\Security\Role;

use Netgen\BlockManager\Ez\Security\Role\RoleHierarchy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Role\Role;

class RoleHierarchyTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Security\Role\RoleHierarchy::__construct
     *
     * @param \Symfony\Component\Security\Core\Role\Role[] $startingRoles
     * @param \Symfony\Component\Security\Core\Role\Role[] $reachableRoles
     *
     * @dataProvider getReachableRolesProvider
     */
    public function testGetReachableRoles(array $startingRoles, array $reachableRoles)
    {
        $role = new RoleHierarchy(
            array(
                'ROLE_NGBM_ADMIN' => array(
                    'ROLE_NGBM_EDITOR',
                ),
                'ROLE_NGBM_EDITOR' => array(
                    'ROLE_NGBM_API',
                ),
            )
        );

        $this->assertEquals($reachableRoles, $role->getReachableRoles($startingRoles));
    }

    public function getReachableRolesProvider()
    {
        return array(
            array(
                array(new Role('ROLE_NGBM_ADMIN')),
                array(new Role('ROLE_NGBM_ADMIN')),
            ),
            array(
                array(new Role('ROLE_NGBM_EDITOR')),
                array(new Role('ROLE_NGBM_EDITOR'), new Role('ROLE_NGBM_ADMIN')),
            ),
            array(
                array(new Role('ROLE_NGBM_API')),
                array(new Role('ROLE_NGBM_API'), new Role('ROLE_NGBM_EDITOR'), new Role('ROLE_NGBM_ADMIN')),
            ),
        );
    }
}
