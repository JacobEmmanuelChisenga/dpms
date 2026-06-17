<?php

namespace App\Policies;

use App\Models\Driver;
use App\Models\User;

class DriverPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER, User::ROLE_MANAGEMENT);
    }

    public function view(User $user, Driver $driver): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER, User::ROLE_MANAGEMENT);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER);
    }

    public function update(User $user, Driver $driver): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER);
    }

    public function delete(User $user, Driver $driver): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER);
    }

    public function archive(User $user, Driver $driver): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER);
    }

    public function restore(User $user, Driver $driver): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER);
    }
}
