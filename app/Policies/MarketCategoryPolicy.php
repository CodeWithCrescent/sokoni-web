<?php

namespace App\Policies;

use App\Models\MarketCategory;
use App\Models\User;

class MarketCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('market-categories.view');
    }

    public function view(User $user, MarketCategory $marketCategory): bool
    {
        return $user->hasPermission('market-categories.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('market-categories.create');
    }

    public function update(User $user, MarketCategory $marketCategory): bool
    {
        return $user->hasPermission('market-categories.edit');
    }

    public function delete(User $user, MarketCategory $marketCategory): bool
    {
        return $user->hasPermission('market-categories.delete');
    }

    public function restore(User $user, MarketCategory $marketCategory): bool
    {
        return $user->hasPermission('market-categories.restore');
    }

    public function forceDelete(User $user, MarketCategory $marketCategory): bool
    {
        return $user->isAdmin();
    }
}
