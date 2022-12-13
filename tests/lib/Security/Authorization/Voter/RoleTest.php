<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Security\Authorization\Voter;

use Netgen\Layouts\Ez\Security\Authorization\Voter\Role;
use PHPUnit\Framework\TestCase;

final class RoleTest extends TestCase
{
    private Role $role;

    protected function setUp(): void
    {
        $this->role = new Role('ROLE_USER');
    }

    /**
     * @covers \Netgen\Layouts\Ez\Security\Authorization\Voter\Role::__construct
     * @covers \Netgen\Layouts\Ez\Security\Authorization\Voter\Role::getRole
     */
    public function testGetRole(): void
    {
        self::assertSame('ROLE_USER', $this->role->getRole());
    }
}
