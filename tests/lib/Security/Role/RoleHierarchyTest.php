<?php

namespace Netgen\BlockManager\Ez\Tests\Security\Role;

use Netgen\BlockManager\Ez\Security\Role\RoleHierarchy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Role\Role;

final class RoleHierarchyTest extends TestCase
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
            [
                'ROLE_NGBM_ADMIN' => [
                    'ROLE_NGBM_EDITOR',
                ],
                'ROLE_NGBM_EDITOR' => [
                    'ROLE_NGBM_API',
                ],
            ]
        );

        $this->assertEquals($reachableRoles, $role->getReachableRoles($startingRoles));
    }

    public function getReachableRolesProvider()
    {
        return [
            [
                [new Role('ROLE_NGBM_ADMIN')],
                [new Role('ROLE_NGBM_ADMIN')],
            ],
            [
                [new Role('ROLE_NGBM_EDITOR')],
                [new Role('ROLE_NGBM_EDITOR'), new Role('ROLE_NGBM_ADMIN')],
            ],
            [
                [new Role('ROLE_NGBM_API')],
                [new Role('ROLE_NGBM_API'), new Role('ROLE_NGBM_EDITOR'), new Role('ROLE_NGBM_ADMIN')],
            ],
        ];
    }
}
