<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Security\Role;

use Netgen\BlockManager\Ez\Security\Role\RoleHierarchy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Role\Role;

final class RoleHierarchyTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Security\Role\RoleHierarchy::__construct
     *
     * @param string $startingRole
     * @param string[] $expectedReachableRoles
     *
     * @dataProvider getReachableRolesProvider
     */
    public function testGetReachableRoles(string $startingRole, array $expectedReachableRoles): void
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

        /** @var \Symfony\Component\Security\Core\Role\Role[] $reachableRoles */
        $reachableRoles = $role->getReachableRoles([new Role($startingRole)]);

        self::assertCount(count($expectedReachableRoles), $reachableRoles);
        self::assertContainsOnlyInstancesOf(Role::class, $reachableRoles);

        foreach ($reachableRoles as $index => $reachableRole) {
            self::assertSame($expectedReachableRoles[$index], $reachableRole->getRole());
        }
    }

    public function getReachableRolesProvider(): array
    {
        return [
            [
                'ROLE_NGBM_ADMIN',
                ['ROLE_NGBM_ADMIN'],
            ],
            [
                'ROLE_NGBM_EDITOR',
                ['ROLE_NGBM_EDITOR', 'ROLE_NGBM_ADMIN'],
            ],
            [
                'ROLE_NGBM_API',
                ['ROLE_NGBM_API', 'ROLE_NGBM_EDITOR', 'ROLE_NGBM_ADMIN'],
            ],
        ];
    }
}
