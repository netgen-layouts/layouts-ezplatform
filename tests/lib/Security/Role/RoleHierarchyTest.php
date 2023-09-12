<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Security\Role;

use Netgen\Layouts\Ez\Security\Role\RoleHierarchy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Role\Role;

use function count;

final class RoleHierarchyTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ez\Security\Role\RoleHierarchy::__construct
     *
     * @param string[] $expectedReachableRoles
     *
     * @dataProvider getReachableRolesDataProvider
     */
    public function testGetReachableRoles(string $startingRole, array $expectedReachableRoles): void
    {
        if (Kernel::VERSION_ID >= 40300) {
            self::markTestSkipped('Test requires Symfony 3.4 to run.');
        }

        $role = new RoleHierarchy(
            [
                'ROLE_NGLAYOUTS_ADMIN' => [
                    'ROLE_NGLAYOUTS_EDITOR',
                ],
                'ROLE_NGLAYOUTS_EDITOR' => [
                    'ROLE_NGLAYOUTS_API',
                ],
            ],
        );

        /** @var \Symfony\Component\Security\Core\Role\Role[] $reachableRoles */
        $reachableRoles = $role->getReachableRoles([new Role($startingRole)]);

        self::assertCount(count($expectedReachableRoles), $reachableRoles);
        self::assertContainsOnlyInstancesOf(Role::class, $reachableRoles);

        foreach ($reachableRoles as $index => $reachableRole) {
            self::assertSame($expectedReachableRoles[$index], $reachableRole->getRole());
        }
    }

    /**
     * @covers \Netgen\Layouts\Ez\Security\Role\RoleHierarchy::__construct
     *
     * @param string[] $expectedReachableRoles
     *
     * @dataProvider getReachableRolesDataProvider
     */
    public function testGetReachableRoleNames(string $startingRole, array $expectedReachableRoles): void
    {
        if (Kernel::VERSION_ID < 40300) {
            self::markTestSkipped('Test requires Symfony 4.3 to run.');
        }

        $role = new RoleHierarchy(
            [
                'ROLE_NGLAYOUTS_ADMIN' => [
                    'ROLE_NGLAYOUTS_EDITOR',
                ],
                'ROLE_NGLAYOUTS_EDITOR' => [
                    'ROLE_NGLAYOUTS_API',
                ],
            ],
        );

        $reachableRoles = $role->getReachableRoleNames([$startingRole]);

        self::assertCount(count($expectedReachableRoles), $reachableRoles);

        foreach ($reachableRoles as $index => $reachableRole) {
            self::assertSame($expectedReachableRoles[$index], $reachableRole);
        }
    }

    public static function getReachableRolesDataProvider(): iterable
    {
        return [
            [
                'ROLE_NGLAYOUTS_ADMIN',
                ['ROLE_NGLAYOUTS_ADMIN'],
            ],
            [
                'ROLE_NGLAYOUTS_EDITOR',
                ['ROLE_NGLAYOUTS_EDITOR', 'ROLE_NGLAYOUTS_ADMIN'],
            ],
            [
                'ROLE_NGLAYOUTS_API',
                ['ROLE_NGLAYOUTS_API', 'ROLE_NGLAYOUTS_EDITOR', 'ROLE_NGLAYOUTS_ADMIN'],
            ],
        ];
    }
}
