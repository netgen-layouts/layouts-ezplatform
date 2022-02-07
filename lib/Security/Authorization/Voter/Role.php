<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Role\Role as BaseRole;

final class Role extends BaseRole
{
    private string $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
