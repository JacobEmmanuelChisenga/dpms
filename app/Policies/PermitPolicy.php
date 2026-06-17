<?php

namespace App\Policies;

use App\Models\Permit;
use App\Models\User;

class PermitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER, User::ROLE_MANAGEMENT);
    }

    public function view(User $user, Permit $permit): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER, User::ROLE_MANAGEMENT);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER);
    }

    public function update(User $user, Permit $permit): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER);
    }

    public function delete(User $user, Permit $permit): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER);
    }

    public function revoke(User $user, Permit $permit): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER);
    }
}
