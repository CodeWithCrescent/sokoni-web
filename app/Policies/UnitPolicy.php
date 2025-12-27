<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('units.view');
    }

    public function view(User $user, Unit $unit): bool
    {
        return $user->hasPermission('units.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('units.create');
    }

    public function update(User $user, Unit $unit): bool
    {
        return $user->hasPermission('units.edit');
    }

    public function delete(User $user, Unit $unit): bool
    {
        return $user->hasPermission('units.delete');
    }

    public function restore(User $user, Unit $unit): bool
    {
        return $user->hasPermission('units.restore');
    }

    public function forceDelete(User $user, Unit $unit): bool
    {
        return $user->isAdmin();
    }
}
