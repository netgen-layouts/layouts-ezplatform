<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Security\Role;

use Netgen\Layouts\Ibexa\Security\Role\RoleHierarchy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function count;

#[CoversClass(RoleHierarchy::class)]
final class RoleHierarchyTest extends TestCase
{
    /**
     * @param string[] $expectedReachableRoles
     */
    #[DataProvider('getReachableRolesDataProvider')]
    public function testGetReachableRoleNames(string $startingRole, array $expectedReachableRoles): void
    {
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

    public static function getReachableRolesDataProvider(): array
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
