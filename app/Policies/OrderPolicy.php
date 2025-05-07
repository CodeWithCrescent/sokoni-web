<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
/**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all orders
        // Customers can view their orders
        // Vendors can view orders containing their products
        // Delivery personnel can view orders assigned to them
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        // Admin can view any order
        if ($user->isAdmin()) {
            return true;
        }
        
        // Customer can view their own orders
        if ($user->isCustomer() && $order->customer_id === $user->id) {
            return true;
        }
        
        // Delivery personnel can view orders assigned to them
        if ($user->isDelivery() && $order->delivery_id === $user->id) {
            return true;
        }
        
        // Vendor can view orders that contain their products
        if ($user->isVendor()) {
            return $order->orderDetails()->whereHas('product', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->exists();
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only customers and admins can create orders
        return $user->isCustomer() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        // Admin can update any order
        if ($user->isAdmin()) {
            return true;
        }
        
        // Delivery personnel can update orders assigned to them (for status updates)
        if ($user->isDelivery() && $order->delivery_id === $user->id) {
            return true;
        }
        
        // Customers can only update their pending orders
        if ($user->isCustomer() && $order->customer_id === $user->id && $order->status === 'pending') {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        // Only admin and the customer who owns the order can cancel it
        // And only if the order is still pending
        return ($user->isAdmin() || 
                ($user->isCustomer() && $order->customer_id === $user->id)) 
                && $order->status === 'pending';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }
}
