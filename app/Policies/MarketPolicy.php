<?php

namespace App\Policies;

use App\Models\Market;
use App\Models\User;

class MarketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('markets.view');
    }

    public function view(User $user, Market $market): bool
    {
        return $user->hasPermission('markets.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('markets.create');
    }

    public function update(User $user, Market $market): bool
    {
        return $user->hasPermission('markets.edit');
    }

    public function delete(User $user, Market $market): bool
    {
        return $user->hasPermission('markets.delete');
    }

    public function restore(User $user, Market $market): bool
    {
        return $user->hasPermission('markets.restore');
    }

    public function forceDelete(User $user, Market $market): bool
    {
        return $user->isAdmin();
    }
}
