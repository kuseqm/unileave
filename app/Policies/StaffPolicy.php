<?php

namespace App\Policies;

use App\Models\Staff;
use App\Models\User;

class StaffPolicy
{
    /**
     * Determine if user can view any staff
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view the staff list
    }

    /**
     * Determine if user can view a specific staff member
     */
    public function view(User $user, Staff $staff): bool
    {
        return true; // All authenticated users can view staff details
    }

    /**
     * Determine if user can create staff
     */
    public function create(User $user): bool
    {
        // Only admin and HR managers can create staff
        return $user->hasRole('admin') || $user->hasRole('hr_manager');
    }

    /**
     * Determine if user can update a specific staff member
     */
    public function update(User $user, Staff $staff): bool
    {
        // Only admin and HR managers can update staff
        return $user->hasRole('admin') || $user->hasRole('hr_manager');
    }

    /**
     * Determine if user can delete a specific staff member
     */
    public function delete(User $user, Staff $staff): bool
    {
        // Only admin can delete staff
        return $user->hasRole('admin');
    }

    /**
     * Check if user has any write permissions (for showing action buttons)
     * This is used for the Actions column header
     */
    public function hasWriteAccess(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('hr_manager');
    }
}
