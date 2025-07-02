<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view any inventory records
     */
    public function viewAny(User $user)
    {
        return in_array($user->role, ['admin', 'supplier', 'manager']);
    }

    /**
     * Determine if user can view an inventory record
     */
    public function view(User $user, Inventory $inventory)
    {
        // Admins can view any inventory
        if ($user->role === 'admin') {
            return true;
        }

        // Suppliers can view inventory for their products
        if ($user->role === 'supplier') {
            return $inventory->product->supplier_id === $user->id;
        }

        // Managers can view all inventory
        return $user->role === 'manager';
    }

    /**
     * Determine if user can create inventory records
     */
    public function create(User $user)
    {
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Determine if user can update an inventory record
     */
    public function update(User $user, Inventory $inventory)
    {
        // Only admins/managers can update inventory, and only if recent
        return in_array($user->role, ['admin', 'manager']) &&
            $inventory->created_at->gt(now()->subHours(24));
    }

    /**
     * Determine if user can delete an inventory record
     */
    public function delete(User $user, Inventory $inventory)
    {
        // Only admins can delete inventory, and only if recent
        return $user->role === 'admin' &&
            $inventory->created_at->gt(now()->subHours(24));
    }
}