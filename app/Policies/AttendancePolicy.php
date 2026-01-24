<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin dapat melihat semua, user hanya miliknya
        return $user->role === 'admin' || $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        // User dapat melihat kehadiran miliknya, admin dapat melihat semua
        return $user->id === $attendance->user_id || $user->role === 'admin' || $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Semua user authenticated dapat membuat attendance record
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        // User biasa hanya dapat update kehadiran miliknya sendiri
        if ($user->role === 'user') {
            return $user->id === $attendance->user_id;
        }
        
        // Admin dapat update kehadiran siapa saja
        return $user->role === 'admin' || $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        // Hanya admin/super admin yang dapat menghapus
        return $user->role === 'admin' || $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attendance $attendance): bool
    {
        // Hanya admin/super admin
        return $user->role === 'admin' || $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attendance $attendance): bool
    {
        // Hanya super admin
        return $user->role === 'super_admin';
    }
}
