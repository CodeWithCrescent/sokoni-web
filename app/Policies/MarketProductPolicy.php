<?php

namespace App\Policies;

use App\Models\MarketProduct;
use App\Models\User;

class MarketProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('market-products.view');
    }

    public function view(User $user, MarketProduct $marketProduct): bool
    {
        return $user->hasPermission('market-products.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('market-products.create');
    }

    public function update(User $user, MarketProduct $marketProduct): bool
    {
        return $user->hasPermission('market-products.edit');
    }

    public function delete(User $user, MarketProduct $marketProduct): bool
    {
        return $user->hasPermission('market-products.delete');
    }

    public function restore(User $user, MarketProduct $marketProduct): bool
    {
        return $user->hasPermission('market-products.restore');
    }

    public function forceDelete(User $user, MarketProduct $marketProduct): bool
    {
        return $user->isAdmin();
    }
}
