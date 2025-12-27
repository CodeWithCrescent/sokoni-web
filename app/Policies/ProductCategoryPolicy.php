<?php

namespace App\Policies;

use App\Models\ProductCategory;
use App\Models\User;

class ProductCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('product-categories.view');
    }

    public function view(User $user, ProductCategory $productCategory): bool
    {
        return $user->hasPermission('product-categories.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('product-categories.create');
    }

    public function update(User $user, ProductCategory $productCategory): bool
    {
        return $user->hasPermission('product-categories.edit');
    }

    public function delete(User $user, ProductCategory $productCategory): bool
    {
        return $user->hasPermission('product-categories.delete');
    }

    public function restore(User $user, ProductCategory $productCategory): bool
    {
        return $user->hasPermission('product-categories.restore');
    }

    public function forceDelete(User $user, ProductCategory $productCategory): bool
    {
        return $user->isAdmin();
    }
}
